(function($) {
	'use strict';
	$(document).ready(function() {
$(".pt_plus_image_parallax_inner_hover").waypoint(function() {	
var dopacity= $(this).attr('data-opacity');
var damount= $(this).attr('data-amount');
var dtype= $(this).attr('data-type');

               $(this).css('opacity',dopacity);		
		var offset = 0;
		if ( dtype === 'tilt' ) {
			offset = - parseInt( damount ) * .6 + '%';
		} else {
			offset = - parseInt( damount ) + 'px';
		}
		$(this).css('top',offset);
		$(this).css('left',offset);
		$(this).css('right',offset);
		$(this).css('bottom',offset);
		$(this).css('width','auto');
		$(this).css('height','auto');
}, { offset: '85%' });
	var elements = document.querySelectorAll('.pt_plus_image_mouse_hover');

	Array.prototype.forEach.call(elements, function(el, i) {
		// find Row
		var row = el.parentNode;
		while ( ! row.classList.contains('vc_row') && ! row.classList.contains('wpb_row') ) {
			if ( row.tagName === 'HTML' ) {
				return;
			}
			row = row.parentNode;
		}
		
		row.style.overflow = 'hidden';
		row.classList.add('image_parallax_row');
		
	});
	

	// Bind to mousemove so animate the hover row
	var elements = document.querySelectorAll('.image_parallax_row');
	Array.prototype.forEach.call(elements, function(row, i) {
		
		row.addEventListener('mousemove', function(e) {
			
			// Get the parent row
			var parentRow = e.target.parentNode;
			while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
				if ( parentRow.tagName === 'HTML' ) {
					return;
				}
				
				parentRow = parentRow.parentNode;
			}
			
			// Get the % location of the mouse position inside the row
			var rect = parentRow.getBoundingClientRect();
			var top = e.pageY - ( rect.top + window.pageYOffset );
			var left = e.pageX  - ( rect.left + window.pageXOffset );
			top /= parentRow.clientHeight;
			left /= parentRow.clientWidth;
			
			// Move all the hover inner divs
			var hoverRows = parentRow.querySelectorAll('.pt_plus_image_parallax_inner_hover');
			Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
			
				// Parameters
				var amount = parseFloat( hoverBg.getAttribute( 'data-amount' ) );
				var inverted = hoverBg.getAttribute( 'data-inverted' ) === 'true';
				var transform;
			
				if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
					
					var rotateY = left * amount - amount / 2;
					var rotateX = ( 1 - top ) * amount - amount / 2;
					if ( inverted ) {
						rotateY = ( 1 - left ) * amount - amount / 2;
						rotateX = top * amount - amount / 2;
					}
					
					transform = 'perspective(2000px) ';
					transform += 'rotateY(' + rotateY + 'deg) ';
					transform += 'rotateX(' + rotateX + 'deg) ';

					hoverBg.style.transition = 'all 0s';
					hoverBg.style.webkitTransform = transform;
					hoverBg.style.transform = transform;
					
				} else {
				
					var moveX = left * amount - amount / 2;
					var moveY = top * amount - amount / 2;
					if ( inverted ) {
						moveX *= -1;
						moveY *= -1;
					}
					transform = 'translate3D(' + moveX + 'px, ' + moveY + 'px, 0) ';

					hoverBg.style.transition = 'all 2s';
					hoverBg.style.webkitTransform = transform;
					hoverBg.style.transform = transform;
				}
				
			});
		});
		
	
		// Bind to mousemove so animate the hover row to it's default state
		row.addEventListener('mouseout', function(e) {
			
			// Get the parent row
			var parentRow = e.target.parentNode;
			while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
				if ( parentRow.tagName === 'HTML' ) {
					return;
				}
				
				parentRow = parentRow.parentNode;
			}
			
			// Reset all the animations
			var hoverRows = parentRow.querySelectorAll('.pt_plus_image_parallax_inner_hover');
			Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {

				var amount = parseFloat( hoverBg.getAttribute( 'data-amount' ) );
			
				hoverBg.style.transition = 'all 5s ease-in-out';
				if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
					hoverBg.style.webkitTransform = 'perspective(2000px) rotateY(0) rotateX(0)';
					hoverBg.style.transform = 'perspective(2000px) rotateY(0) rotateX(0)';
				} else {
					hoverBg.style.webkitTransform = 'translate3D(0, 0, 0)';
					hoverBg.style.transform = 'translate3D(0, 0, 0)';
				}
				
			});
		});
	});
/*--------------------------------------------------------------------------------------------------------------------------*/
var elements = document.querySelectorAll('.pt_plus_mordern_image_parallax');

	Array.prototype.forEach.call(elements, function(el, i) {
		// find Row
		var row = el.parentNode;
		while ( ! row.classList.contains('vc_row') && ! row.classList.contains('wpb_row') ) {
			if ( row.tagName === 'HTML' ) {
				return;
			}
			row = row.parentNode;
		}
		
		row.style.overflow = 'hidden';
		row.classList.add('image_parallax_row');		
	});
// Bind to mousemove so animate the hover row
	var elements = document.querySelectorAll('.image_parallax_row');
	Array.prototype.forEach.call(elements, function(row, i) {
		
		row.addEventListener('mousemove', function(e) {
			
			// Get the parent row
			var parentRow = e.target.parentNode;
			while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
				if ( parentRow.tagName === 'HTML' ) {
					return;
				}
				
				parentRow = parentRow.parentNode;
			}
			
			// Get the % location of the mouse position inside the row
			var rect = parentRow.getBoundingClientRect();
			var top = e.pageY - ( rect.top + window.pageYOffset );
			var left = e.pageX  - ( rect.left + window.pageXOffset );
			top /= parentRow.clientHeight;
			left /= parentRow.clientWidth;
			
			// Move all the hover inner divs
			var hoverRows = parentRow.querySelectorAll('.parallax_image');
			Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
			
				// Parameters
				var amount = parseFloat( hoverBg.getAttribute( 'data-parallax' ) );
				TweenLite.to( hoverBg, 0.2, {x : -(( e.clientX - (window.innerWidth/2) ) / amount ),y : -(( e.clientY - (window.innerHeight/2) ) / amount ) });
			});
		});
		
	
		// Bind to mousemove so animate the hover row to it's default state
		row.addEventListener('mouseout', function(e) {
			
			// Get the parent row
			var parentRow = e.target.parentNode;
			while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
				if ( parentRow.tagName === 'HTML' ) {
					return;
				}
				
				parentRow = parentRow.parentNode;
			}
			
			// Reset all the animations
			var hoverRows = parentRow.querySelectorAll('.parallax_image');
			Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {

				var amount = parseFloat( hoverBg.getAttribute( 'data-parallax' ) );
			
				TweenLite.to( hoverBg, 0.2, {x : -(( e.clientX - (window.innerWidth/2) ) / amount ),y : -(( e.clientY - (window.innerHeight/2) ) / amount ) });
				
				
			});
		});
	});
	
});
})(jQuery);
/*------------------------ row full height----------------------*/
(function($) {
'use strict';
	$(window).load(function() {
		$(".vc_row-flex").each(function() {

		if($(this).hasClass('vc_row-o-columns-middle')){
			$(this).removeClass('vc_row-o-columns-middle');
			$(this).addClass('vc_row-o-columns-stretch vc_row-o-equal-height');
			$('.vc_column_container',this).css('align-items','center');
			$('.vc_column_container',this).css('-webkit-align-content','center');
			$('.vc_column_container',this).css('-ms-flex-line-pack','center');
		}
		if($(this).hasClass('vc_row-o-columns-top')){
			$(this).removeClass('vc_row-o-columns-top');
			$(this).addClass('vc_row-o-columns-stretch vc_row-o-equal-height');
		}
		if($(this).hasClass('vc_row-o-columns-bottom')){
			$(this).removeClass('vc_row-o-columns-bottom');
			$(this).addClass('vc_row-o-columns-stretch vc_row-o-equal-height');
			$('.vc_column_container',this).css('align-items','flex-end');
			$('.vc_column_container',this).css('-webkit-align-content','flex-end');
			$('.vc_column_container',this).css('-ms-flex-line-pack','flex-end');
		}

		});

	});
})(jQuery);
/*------------------------ row full height----------------------*/

/*------------------------ vc columns image----------------------*/
(function($) {
'use strict';
	$(document).ready(function() {
		$('.columns-bg-image.columns_animated_bg,.image-bgscroll').each(function() {
			var $self = $(this),
				dir = $self.data('direction'),
				speed = 100 - $self.data('parallax_sense'),
				coords = 0,
				mobileEnabled = ($self.data('mobile_enable') && $self.data('mobile_enable') == '1') ? true : false;

			if(!mobileEnabled && Modernizr.touch) return;
			
			setInterval(function() {
				if(dir == 'left' || dir == 'bottom')
					coords -= 1;
				else
					coords += 1;
				if(dir == 'left' || dir == 'right')
					$self.css('backgroundPosition', coords +'px 50%');
				else
					$self.css('backgroundPosition', '50% '+ coords + 'px');
			}, speed);
		});

$(".vc_parallax").each(function() {
 var img_fixed=$(this).attr('data-fixed');
if(img_fixed=='fixed'){
$(this).find(".vc_parallax-inner").css('background-attachment',img_fixed);
}
});
});
})(jQuery);
/*------------------------ vc columns image----------------------*/
/*---------------------------vc columns video----------------------------*/

(function($) {
	$.fn.pt_plus_VideoBgInit = function() {
		return this.each(function() {
			var $self = $(this),
				ratio = 1.778,
				pWidth = $self.parent().width(),
				pHeight = $self.parent().height(),
				selfWidth,
				selfHeight;
			var setSizes = function() {
				if(pWidth / ratio < pHeight) {
					selfWidth = Math.ceil(pHeight * ratio);
					selfHeight = pHeight;
					$self.css({
						'width': selfWidth,
						'height': selfHeight
					});
				} else {
					selfWidth = pWidth;
					selfHeight = Math.ceil(pWidth / ratio);
					$self.css({
						'width': selfWidth,
						'height': selfHeight
					});
				}
			};				
			setSizes();
			$(window).on('resize', setSizes);
		});
	};

	$(window).load(function() {
	setTimeout(function(){
		$('.columns-video-bg video, .columns-video-bg .columns-bg-frame').pt_plus_VideoBgInit();
$('.self-hosted-videos').each(function() {
var $self=$(this);
$self[0].play();
});
}, 100);
      if($('.columns-youtube-bg').length > 0) {
		var tag = document.createElement('script');

		tag.src = "//www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		
		var players = {};
		
		window.onYouTubeIframeAPIReady = function() {
			$('.columns-youtube-bg iframe').each(function() {
				var $self = $(this),
					id = $self.attr('id');
					players[id] = new YT.Player(id, {   
					       playerVars: {autoplay:1},    
						events: {
						   onReady: function(e) {
						   if($self.data('muted') && $self.data('muted') == '1') {
						      e.target.mute();
						   }
						      e.target.playVideo();
						   }
						}
					});
				
			});
		};
		
	}
	if($('.columns-vimeo-bg').length > 0) {
	
		$(document).ready(function() {
			$('.columns-vimeo-bg iframe').each(function() {
				var $self = $(this);
					
				if (window.addEventListener) {
					window.addEventListener('message', onMessageReceived, false);
				} else {
					window.attachEvent('onmessage', onMessageReceived, false);
				}
		
				function onMessageReceived(e) {
					var data = JSON.parse(e.data);
					
					switch (data.event) {
						case 'ready':
							$self[0].contentWindow.postMessage('{"method":"play", "value":1}','*');
							if($self.data('muted') && $self.data('muted') == '1') {
								$self[0].contentWindow.postMessage('{"method":"setVolume", "value":0}','*');
							}
							break;
					}
				}
			});
		});
	}
	});
})(jQuery);
/*---------------------------vc columns video----------------------------*/
(function($) {
'use strict';
$(window).load(function() {
	var elements = document.querySelectorAll('.pt_plus_image_mouse_hover');

	Array.prototype.forEach.call(elements, function(el, i) {
		// find Row
		var row = el.parentNode;
		while ( ! row.classList.contains('vc_row') && ! row.classList.contains('wpb_row') ) {
			if ( row.tagName === 'HTML' ) {
				return;
			}
			row = row.parentNode;
		}
		
		row.style.overflow = 'hidden';
		row.classList.add('image_parallax_row');
		
		var div =  document.querySelector(".pt_plus_image_parallax_inner_hover");

var dopacity= $('.pt_plus_image_parallax_inner_hover').attr('data-opacity');
var damount= $('.pt_plus_image_parallax_inner_hover').attr('data-amount');
var dtype= $('.pt_plus_image_parallax_inner_hover').attr('data-type');
		div.style.opacity=dopacity;		
		var offset = 0;
		if ( div.getAttribute('data-type') === 'tilt' ) {
			offset = - parseInt( damount ) * .6 + '%';
		} else {
			offset = - parseInt( damount ) + 'px';
		}
		div.style.top=offset;
		div.style.left=offset;
		div.style.right=offset;
		div.style.bottom=offset;
		div.style.width='auto';
		div.style.height='auto';
		
	});
	
	
	// Disable hover rows in mobile
	function _isMobile() {
		return ( Modernizr.touch && jQuery(window).width() <= 1000 ) || // touch device estimate
	 	 	   ( window.screen.width <= 1040 && window.devicePixelRatio > 1 ); // device size estimate
	}
	if ( _isMobile() ) {
		return;
	}
	
	
	// Bind to mousemove so animate the hover row
	var elements = document.querySelectorAll('.image_parallax_row');
	Array.prototype.forEach.call(elements, function(row, i) {
		
		row.addEventListener('mousemove', function(e) {
			
			// Get the parent row
			var parentRow = e.target.parentNode;
			while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
				if ( parentRow.tagName === 'HTML' ) {
					return;
				}
				
				parentRow = parentRow.parentNode;
			}
			
			// Get the % location of the mouse position inside the row
			var rect = parentRow.getBoundingClientRect();
			var top = e.pageY - ( rect.top + window.pageYOffset );
			var left = e.pageX  - ( rect.left + window.pageXOffset );
			top /= parentRow.clientHeight;
			left /= parentRow.clientWidth;
			
			// Move all the hover inner divs
			var hoverRows = parentRow.querySelectorAll('.pt_plus_image_parallax_inner_hover');
			Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
			
				// Parameters
				var amount = parseFloat( hoverBg.getAttribute( 'data-amount' ) );
				var inverted = hoverBg.getAttribute( 'data-inverted' ) === 'true';
				var transform;
			
				if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
					
					var rotateY = left * amount - amount / 2;
					var rotateX = ( 1 - top ) * amount - amount / 2;
					if ( inverted ) {
						rotateY = ( 1 - left ) * amount - amount / 2;
						rotateX = top * amount - amount / 2;
					}
					
					transform = 'perspective(2000px) ';
					transform += 'rotateY(' + rotateY + 'deg) ';
					transform += 'rotateX(' + rotateX + 'deg) ';

					hoverBg.style.transition = 'all 0s';
					hoverBg.style.webkitTransform = transform;
					hoverBg.style.transform = transform;
					
				} else {
				
					var moveX = left * amount - amount / 2;
					var moveY = top * amount - amount / 2;
					if ( inverted ) {
						moveX *= -1;
						moveY *= -1;
					}
					transform = 'translate3D(' + moveX + 'px, ' + moveY + 'px, 0) ';

					hoverBg.style.transition = 'all 0.3s ease-in-out';
					hoverBg.style.webkitTransform = transform;
					hoverBg.style.transform = transform;
				}
				
			});
		});
		
	
		// Bind to mousemove so animate the hover row to it's default state
		row.addEventListener('mouseout', function(e) {
			
			// Get the parent row
			var parentRow = e.target.parentNode;
			while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
				if ( parentRow.tagName === 'HTML' ) {
					return;
				}
				
				parentRow = parentRow.parentNode;
			}
			
			// Reset all the animations
			var hoverRows = parentRow.querySelectorAll('.pt_plus_image_parallax_inner_hover');
			Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {

				var amount = parseFloat( hoverBg.getAttribute( 'data-amount' ) );
			
				hoverBg.style.transition = 'all 3s ease-in-out';
				if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
					hoverBg.style.webkitTransform = 'perspective(2000px) rotateY(0) rotateX(0)';
					hoverBg.style.transform = 'perspective(2000px) rotateY(0) rotateX(0)';
				} else {
					hoverBg.style.webkitTransform = 'translate3D(0, 0, 0)';
					hoverBg.style.transform = 'translate3D(0, 0, 0)';
				}
				
			});
		});
	});
/*--------------------------------------------------------------------------------------------------------------------------*/
// Bind to mousemove so animate the hover row
	var elements = document.querySelectorAll('.image_parallax_row');
	Array.prototype.forEach.call(elements, function(row, i) {
		
		row.addEventListener('mousemove', function(e) {
			
			// Get the parent row
			var parentRow = e.target.parentNode;
			while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
				if ( parentRow.tagName === 'HTML' ) {
					return;
				}
				
				parentRow = parentRow.parentNode;
			}
			
			// Get the % location of the mouse position inside the row
			var rect = parentRow.getBoundingClientRect();
			var top = e.pageY - ( rect.top + window.pageYOffset );
			var left = e.pageX  - ( rect.left + window.pageXOffset );
			top /= parentRow.clientHeight;
			left /= parentRow.clientWidth;
			
			// Move all the hover inner divs
			var hoverRows = parentRow.querySelectorAll('.parallax_image');
			Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {
			
				// Parameters
				var amount = parseFloat( hoverBg.getAttribute( 'data-parallax' ) );
				TweenLite.to( hoverBg, 0.2, {x : -(( e.clientX - (window.innerWidth/2) ) / amount ),y : -(( e.clientY - (window.innerHeight/2) ) / amount ) });
			});
		});
		
	
		// Bind to mousemove so animate the hover row to it's default state
		row.addEventListener('mouseout', function(e) {
			
			// Get the parent row
			var parentRow = e.target.parentNode;
			while ( ! parentRow.classList.contains('image_parallax_row') ) {
						
				if ( parentRow.tagName === 'HTML' ) {
					return;
				}
				
				parentRow = parentRow.parentNode;
			}
			
			// Reset all the animations
			var hoverRows = parentRow.querySelectorAll('.parallax_image');
			Array.prototype.forEach.call(hoverRows, function(hoverBg, i) {

				var amount = parseFloat( hoverBg.getAttribute( 'data-parallax' ) );
			
				TweenLite.to( hoverBg, 0.2, {x : -(( e.clientX - (window.innerWidth/2) ) / amount ),y : -(( e.clientY - (window.innerHeight/2) ) / amount ) });
				
				
			});
		});
	});
	
});
})(jQuery);

/*---------row canvas style 2-----------------*/
(function($) {
'use strict';
$(window).load(function() {
if ($("#pt-plus-row-canvas-2").length) {
			var can2_color =$("#pt-plus-row-canvas-2").attr('data-color');
			particlesJS("pt-plus-row-canvas-2",{particles:{number:{value:80,density:{enable:!0,value_area:800}},color:{value:can2_color},shape:{type:"circle",stroke:{width:0,color:"#000000"},polygon:{nb_sides:5},image:{src:"img/github.svg",width:100,height:100}},opacity:{value:.5,random:!1,anim:{enable:!1,speed:1,opacity_min:.1,sync:!1}},size:{value:2,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!0,distance:150,color:can2_color,opacity:.4,width:1},move:{enable:!0,speed:2,direction:"none",random:!1,straight:!1,out_mode:"out",bounce:!1,attract:{enable:!1,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:!0,mode:"grab"},onclick:{enable:!0,mode:"push"},resize:!0},modes:{grab:{distance:150,line_linked:{opacity:1}},bubble:{distance:400,size:40,duration:2,opacity:8,speed:3},repulse:{distance:200,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
		}
});
})(jQuery);
/*---------row canvas style 2-----------------*/

(function($) {
'use strict';
$(window).load(function() {
/*---------canvas style 3------------------*/
if ($("#pt-plus-row-canvas-5").length) {
var can_color =$("#pt-plus-row-canvas-5").attr('data-color');
particlesJS("pt-plus-row-canvas-5",{particles:{number:{value:600,density:{enable:!0,value_area:800}},color:{value:can_color},shape:{type:"circle",stroke:{width:0,color:"#000000"},polygon:{nb_sides:5},image:{src:"",width:100,height:100}},opacity:{value:0,random:!1,anim:{enable:!1,speed:0,opacity_min:0,sync:!1}},size:{value:3,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!0,distance:32.06824121731046,color:can_color,opacity:.8,width:1},move:{enable:!0,speed:4,direction:"none",random:!1,straight:!1,out_mode:"out",bounce:!1,attract:{enable:!1,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:!1,mode:"repulse"},onclick:{enable:!1,mode:"push"},resize:!0},modes:{grab:{distance:400,line_linked:{opacity:1}},bubble:{distance:200,size:140,duration:2,opacity:8,speed:2},repulse:{distance:100,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
}
/*----------------canvas style 3-------------*/
/*----------------canvas style 5-------------*/
if ($("#pt-plus-row-canvas-3").length) {
var can3_color =$("#pt-plus-row-canvas-3").attr('data-color');
var can3_type=$("#pt-plus-row-canvas-3").attr('data-type');
particlesJS("pt-plus-row-canvas-3", {"particles":{"number":{"value":80,"density":{"enable":true,"value_area":800}},"color":{"value":can3_color},"shape":{"type":can3_type,"stroke":{"width":4,"color":can3_color},"polygon":{"nb_sides":8},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":2,"random":true,"anim":{"enable":false,"speed":102.32172874996948,"size_min":25.174393581341697,"sync":true}},"line_linked":{"enable":true,"distance":150,"color":can3_color,"opacity":0.4,"width":1},"move":{"enable":true,"speed":6,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":923.0769230769231,"line_linked":{"opacity":1}},"bubble":{"distance":287.7122877122877,"size":40,"duration":3.9160839160839163,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
}
/*----------------canvas style 5-------------*/
/*----------------canvas style 6-------------*/
if ($("#pt-plus-row-canvas-4").length) {
var can4_color =$("#pt-plus-row-canvas-4").attr('data-color');
var can4_type=$("#pt-plus-row-canvas-4").attr('data-type');
particlesJS("pt-plus-row-canvas-4", {"particles":{"number":{"value":10,"density":{"enable":true,"value_area":800}},"color":{"value":can4_color},"shape":{"type":can4_type,"stroke":{"width":0,"color":can4_color},"polygon":{"nb_sides":5},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.5050747991726396,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":100.21325380409519,"random":true,"anim":{"enable":true,"speed":10,"size_min":40,"sync":false}},"line_linked":{"enable":false,"distance":481.0236182596568,"color":can4_color,"opacity":1,"width":2},"move":{"enable":true,"speed":8,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"bubble"},"onclick":{"enable":false,"mode":"push"},"resize":true},"modes":{"grab":{"distance":431.5684315684316,"line_linked":{"opacity":0.3642810306724629}},"bubble":{"distance":263.73626373626377,"size":55.94405594405595,"duration":2.1578421578421576,"opacity":0.3356643356643357,"speed":3},"repulse":{"distance":239.76023976023978,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
}
/*----------------canvas style 6-------------*/
/*----------------canvas style 7-------------*/
if ($("#pt-plus-row-canvas-7").length) {
var can7_color =$("#pt-plus-row-canvas-7").attr('data-color');
var can7_type=$("#pt-plus-row-canvas-7").attr('data-type');
particlesJS("pt-plus-row-canvas-7", {"particles":{"number":{"value":400,"density":{"enable":true,"value_area":2840.9315098761817}},"color":{"value":can7_color},"shape":{"type":can7_type,"stroke":{"width":0,"color":can7_color},"polygon":{"nb_sides":5},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.5,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":11,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":224.4776885211732,"color":can7_color,"opacity":0.1683582663908799,"width":1.2827296486924182},"move":{"enable":true,"speed":3,"direction":"bottom","random":true,"straight":false,"out_mode":"bounce","bounce":false,"attract":{"enable":false,"rotateX":881.8766334760375,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"bubble"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":0.5}},"bubble":{"distance":400,"size":4,"duration":0.3,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
}
/*----------------canvas style 7-------------*/
});
$(window).resize(function(){
setTimeout(function(){
		$(".particles-js-canvas-el").each(function() {	
				var IW = window.innerWidth;
				var IH = $(this).parents('.vc_row').height();
					$(this).css('height',IH);
					$(this).css('width',IW);
		});
}, 100);
});
})(jQuery);

/*-----------------------------mordern parallax image-------------------*/
(function($) {
'use strict';
$(window).resize(function(){
$(".parallax_image").each(function() {
var win_width = $(window).width();

var tablet_w=$(this).attr('data-tablet-width');
var mobile_w=$(this).attr('data-mobile-width');
if(win_width<768){
$(this).css('width',tablet_w);
}
if(win_width<480){
$(this).css('width',mobile_w);
}
if(win_width>=768){
$(this).css('width','auto');
}
});
$(".mordern-image-effect").each(function() {
var win_width = $(window).width();

var tab_w=$(this).attr('data-tablet-width');
var mob_w=$(this).attr('data-mobile-width');
if(win_width<768){
$(this).css('width',tab_w);
}
if(win_width<480){
$(this).css('width',mob_w);
}
if(win_width>=768){
$(this).css('width','auto');
}
});

});
})(jQuery);
/*-----------------------------mordern parallax image-------------------*/
(function($) {
'use strict';
	$(window).load(function() {
		$(".pt-plus-row-set").each(function() {
		var get_name=$(this).data("get-name");
		if(get_name=='Jupiter Child Theme' || get_name=='Jupiter'){
			var parent_row= $(this).parent().parent().parent('.vc_row');		
		}else if(get_name=='Salient Child Theme' || get_name=='Salient'){
			var parent_row= $(this).parent().parent().parent().parent().parent('.vc_row');		
		}else{
			var parent_row= $(this).parent().parent().parent().parent('.vc_row');
		}
		if(parent_row){
		$( parent_row ).prepend($(this));	
		parent_row.css("position","relative");
		}
		});
	});

})(jQuery);
;(function($, window, document, undefined) {
	$.fn.animatedBG = function(options){
		var defaults = {
				colorSet: ['#ef008c', '#00be59', '#654b9e', '#ff5432', '#00d8e6'],
				speed: 3000
			},
			settings = $.extend({}, defaults, options);

		return this.each(function(){
			var $this = $(this);

			$this.each(function(){
				var $el = $(this),
					colors = settings.colorSet;
				
				function shiftColor() {
					var color = colors.shift();
					colors.push(color);
					return color;
				}

				// initial color
				var initColor = shiftColor();
				$el.css('backgroundColor', initColor);
				setInterval(function(){
					var color = shiftColor();
					$el.animate({backgroundColor: color}, 3000);
				}, settings.speed);
			});
		});
	};
	$(function(){
		$(document).ready(function() {
		$(".row-animated-bg").each(function() {
			var data_id= $(this).data('id');
			var data_time=$(this).data('bg-time');
			var colors =$(this).data('bg');
			$('.'+data_id).animatedBG({
				colorSet: colors,
				speed: data_time
			});
		});
		});
	});
}(jQuery, window, document));
/*-----------------------------bg imageclip-------------------*/
(function($) {
'use strict';
$(document).ready(function() {
$(".pt-plus-row-imageclip").each(function() {
var data_id= $(this).data('id');
var border_width= $(this).data('border-width');
var border_style= $(this).data('border-style');
var border_color= $(this).data('border-color');
var box_shadow=$(this).data('box-shadow');
$('head').append('<style >.'+data_id+' .segmenter__shadow{border-width:'+border_width+';border-style:'+border_style+';border-color:'+border_color+';box-shadow:'+box_shadow+';}</style>');

});

});
})(jQuery);
/*-----------------------------bg imageclip-------------------*/