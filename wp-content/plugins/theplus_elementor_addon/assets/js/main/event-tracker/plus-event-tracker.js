/*event tracker*/
(function($) {
    "use strict";
    $(document).ready(function () {
        plus_event_tracker($);
    });
})( jQuery );

function plus_event_tracker($) {
    "use strict";

    function log(str='') {
        if(window.debug_track) {
            console.log('[Plus-Event-Tracker] - ' + str);
        }
    }

    function facebooktrack(type, event, source) {
        log("fbq('" + type + "', '" + event + "')");
        if(window.fbq && typeof(fbq) === 'function') {
            fbq(type, event);
        } else {
            console.error('Error : Facebook event, fbq is not defined');
        }
    }
	
    function gototrack(element, options) {
		if(options != undefined ){
			if(options['plus-track-fb-event']) {			
				var event = options['plus-fb-event'];			
				if(event === 'Custom') {
					var customevent = options['plus-fb-event-custom'];
					facebooktrack('trackCustom', customevent, element);				
				}else {
					facebooktrack('track', event, element);
				}			
			}
		}
    }

    function loadtracker(element,theplus_event_tracker) {      
		if($(element).find('a').length > 0)  {
			$(element).find('a').each(function () {
				$(this).on('click', function () {
					gototrack(this, theplus_event_tracker);
				});
			});
		}else {
            $(element).on('click:not(.wpcf7-submit):not(.everest-forms-submit-button):not(.wpforms-submit)', function () {
                gototrack(this, theplus_event_tracker);
            });
        }	
    }

    $('.theplus-event-tracker').each(function () {
        var theplus_event_tracker = $(this).data('theplus-event-tracker');
        loadtracker(this, theplus_event_tracker);
    });
	
	if($('.wpcf7-form .wpcf7-submit').length ){
		$('.wpcf7-form .wpcf7-submit').on('click', function(){
			var new_get_data = $(this).closest('.theplus-event-tracker').data('theplus-event-tracker');
			gototrack(this, new_get_data);
		});
	}
	
	if($('.caldera_forms_form .btn').length ){
		$('.caldera_forms_form .btn').on('click', function(){
			var new_get_data = $(this).closest('.theplus-event-tracker').data('theplus-event-tracker');
			gototrack(this, new_get_data);
		});
	}
	
	if($('.everest-form .everest-forms-submit-button').length ){
		$('.everest-form .everest-forms-submit-button').on('click', function(){
			var new_get_data = $(this).closest('.theplus-event-tracker').data('theplus-event-tracker');
			gototrack(this, new_get_data);
		});
	}
	
	if($('.wpforms-form .wpforms-submit').length ){
		$('.wpforms-form .wpforms-submit').on('click', function(){
			var new_get_data = $(this).closest('.theplus-event-tracker').data('theplus-event-tracker');
			gototrack(this, new_get_data);
		});
	}
	
	if($('.elementor-form .elementor-button').length ){
		$('.elementor-form .elementor-button').on('click', function(){
			var new_get_data = $(this).closest('.theplus-event-tracker').data('theplus-event-tracker');
			gototrack(this, new_get_data);
		});
	}
	
	if($('.elementor-widget-video .elementor-open-inline .elementor-custom-embed-image-overlay').length ){
		$('.elementor-widget-video .elementor-open-inline .elementor-custom-embed-image-overlay').on('click', function(){
			var new_get_data = $(this).closest('.theplus-event-tracker').data('theplus-event-tracker');
			gototrack(this, new_get_data);
		});
	}
	
	if($('.ts-video-wrapper .ts-video-play-btn').length ){
		$('.ts-video-wrapper .ts-video-play-btn').on('click', function(){
			var new_get_data = $(this).closest('.theplus-event-tracker').data('theplus-event-tracker');
			gototrack(this, new_get_data);
		});
	}

    /**Google Analytics*/
    let getAllEle = document.querySelectorAll('.theplus-event-tracker');
    if(getAllEle){
        getAllEle.forEach(function(self){
            let aEvtAtt = JSON.parse(self.getAttribute('data-theplus-event-tracker')),
                googleID = aEvtAtt.google_analytics_id,
                GoogleSwitch = aEvtAtt.google;

				if( GoogleSwitch ){
					if( !googleID ){
						let message = "Please Add Google ID for GA4 in the plus > Extra Options > Google Track ID"
							console.log(`%c${message}`, 'color: purple; font-size: 20px; font-weight: bold;');
					}

					if( googleID ){
						var gtagSc = document.createElement('script');
							gtagSc.src = `https://www.googletagmanager.com/gtag/js?id=${googleID}`;
							gtagSc.async = true;
							gtagSc.onload = function() {
								var gtagScNew = document.createElement('script');
								gtagScNew.innerHTML = `window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag("js", new Date()); gtag("config", "${googleID}", { "debug_mode":true });`;
								document.head.appendChild(gtagScNew);
							};
							document.head.appendChild(gtagSc);
					}
				}

        })
    }

    let getAllElement = document.querySelectorAll('.theplus-event-tracker');
    if(getAllElement.length > 0){
        getAllElement.forEach((evt)=>{
            let aEvtAttr = JSON.parse(evt.getAttribute('data-theplus-event-tracker')),
				GoogleSwitch = aEvtAttr.google;

				if( GoogleSwitch ){
					/*Target all link tags*/
					let formClass = [ '.wpcf7-submit', '.ts-video-play-btn', '.wpforms-submit', '.everest-forms-submit-button','a', '.elementor-button']
						formClass.forEach((aEl)=>{
							let innerVideo = evt.querySelector(aEl);
							if(innerVideo){
								innerVideo.addEventListener('click', ()=>{
									eventGFLoad(aEvtAttr);
								});
							}
						});
				}
        });
    }
    
    function eventGFLoad(aEvtAttr){
        if(aEvtAttr && aEvtAttr.google){
            
            if(typeof(gtag) === 'function'){
                let eProps = aEvtAttr.eventProperties,
                	myObject = {};

                    if(eProps){
                        eProps.forEach((data)=>{
                            myObject = Object.assign({}, myObject, {[data.eventName] : data.eventValue});
                        });
                        console.log( myObject );
                    }

                    if(aEvtAttr.gglEventType && aEvtAttr.gglEventType=='recommended'){
                        gtag('event', aEvtAttr.gglSelEvent , myObject);
                    }else{
                        console.log(aEvtAttr.gCsmEventName)
                        gtag('event', aEvtAttr.gCsmEventName, myObject);
                    }
            }else{
                console.log('Error : Google event, gtag is not defined');
            }
        }
    
    }
}