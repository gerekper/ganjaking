( function( $ ) {	
	var WidgetRowBackgroundHandler = function ($scope, $) {
		var row_bg_elem = $scope.find('.pt-plus-row-set').eq(0);
 
		var parent_row= row_bg_elem.closest('section.elementor-element,.elementor-element.e-container.e-container--column,.elementor-element.e-con');
		var wid_sec=$scope.closest('section.elementor-element,.elementor-element.e-container.e-container--column,.elementor-element.e-con');
 
		if(wid_sec.length){
			var widget_remove_old=$(wid_sec).find("> .pt-plus-row-set");
			widget_remove_old.remove();
			var remove_page_gradient=$scope.closest('.elementor').find("> .plus-row-bg-gradient");
			remove_page_gradient.remove();	
			var scroll_section_bg=$scope.closest('.elementor').find("> .plus-scroll-sections-bg");
			scroll_section_bg.remove();			
		}
		var animate_gradient= $scope.find('.pt-plus-row-set .plus-row-bg-gradient').data('full-page');
		if(animate_gradient=='yes'){
			var page_gradient= $scope.find('.plus-row-bg-gradient');
			var position=page_gradient.data("position");
			row_bg_elem.closest('.elementor').prepend(page_gradient);
			row_bg_elem.closest('.elementor').css("position",position);
			if($.isFunction(window.plus_onscroll_bg)){
				plus_onscroll_bg();
			}			
		}
		if(parent_row){
			if($scope.find('.pt-plus-row-set .plus-scroll-sections-bg').length>0){
				var scroll_sec_bg= $scope.find('.plus-scroll-sections-bg');
				var position=scroll_sec_bg.data("position");
				row_bg_elem.closest('.elementor').prepend(scroll_sec_bg);
				row_bg_elem.closest('.elementor').css("position",position);
				if($.isFunction(window.plus_onscroll_bg)){
					plus_onscroll_bg();
				}
			}			
			$( parent_row ).prepend(row_bg_elem);
			parent_row.css("position","relative");
			var bg_sec=$(wid_sec).find("> .pt-plus-row-set");
			if(bg_sec.data("section-hidden") !=undefined || bg_sec.data("section-hidden") !=''){
				if(!parent_row.hasClass("elementor-element-edit-mode")){
					parent_row.css("overflow",bg_sec.attr("data-section-hidden"));
				}
			}
		}
		if(wid_sec.find(".snow-particles").length){
			var snow_particles = document.querySelector('.snow-particles');
			snow_particles_background($('.snow-particles').parent(), snow_particles);
		}
		/*auto moving image*/
		if(wid_sec.find(".columns-bg-image.columns_animated_bg,.image-bgscroll").length){
			$('.columns-bg-image.columns_animated_bg,.image-bgscroll').each(function() {
				var $self = $(this),
				dir = $self.data('direction'),
				speed = 100 - $self.data('parallax_sense'),
				coords = 0,
				mobileEnabled = ($self.data('mobile_enable') && $self.data('mobile_enable') == '1') ? true : false;
 
				//if(!mobileEnabled && Modernizr.touch) return;
 
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
		}
		/*auto moving image*/
		/* mouse tilt parallax image*/
		if(wid_sec.find(".pt_plus_image_parallax_inner_hover").length){
 
			//$(".pt_plus_image_parallax_inner_hover").waypoint(function() {
				$(".pt_plus_image_parallax_inner_hover").each(function(){
					var dopacity= $(this).attr('data-opacity');
					var damount= $(this).attr('data-amount');
					var dperspective= $(this).attr('data-perspective');
					var dscale= $(this).attr('data-scale');
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
					$(this).css('transform','scale('+dscale+') perspective('+dperspective+'px)');
				});
		//}, { offset: '85%' });
			var elements = document.querySelectorAll('.pt_plus_image_mouse_hover');
 
			Array.prototype.forEach.call(elements, function(el, i) {
				// find Row
				var row = el.parentNode;
				//row.style.overflow = 'hidden';
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
						var dperspective = parseFloat( hoverBg.getAttribute( 'data-perspective' ) );
						var dscale = parseFloat( hoverBg.getAttribute( 'data-scale' ) );
						var inverted = hoverBg.getAttribute( 'data-inverted' ) === 'true';
						var transform;
 
						if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
 
							var rotateY = left * amount - amount / 2;
							var rotateX = ( 1 - top ) * amount - amount / 2;
							if ( inverted ) {
								rotateY = ( 1 - left ) * amount - amount / 2;
								rotateX = top * amount - amount / 2;
							}
 
							transform = 'scale('+dscale+') perspective('+dperspective+'px) ';
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
							transform = 'scale('+dscale+') translate3D(' + moveX + 'px, ' + moveY + 'px, 0) ';
 
							hoverBg.style.transition = 'all 0s';
							hoverBg.style.webkitTransform = transform;
							hoverBg.style.transform = transform;
							}
 
					});
				});
 
 
				// Bind to mousemove so animate the hover
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
						var scale = parseFloat( hoverBg.getAttribute( 'data-scale' ) );
						var perspective = parseFloat( hoverBg.getAttribute( 'data-perspective' ) );
 
						hoverBg.style.transition = 'all 3s ease-in-out';
						if ( hoverBg.getAttribute( 'data-type' ) === 'tilt' ) {
							hoverBg.style.webkitTransform = 'scale('+scale+') perspective('+perspective+'px) rotateY(0) rotateX(0)';
							hoverBg.style.transform = 'scale('+scale+') perspective('+perspective+'px) rotateY(0) rotateX(0)';
							} else {
							hoverBg.style.webkitTransform = 'scale('+scale+') translate3D(0, 0, 0)';
							hoverBg.style.transform = 'scale('+scale+') translate3D(0, 0, 0)';
						}
 
					});
				});
			});
		}
		/* mouse tilt parallax image*/
		/* mouse hover parallax image*/
		if(wid_sec.find(".pt_plus_mordern_image_parallax").length){
			var elements = document.querySelectorAll('.pt_plus_mordern_image_parallax');
 
			Array.prototype.forEach.call(elements, function(el, i) {
				// find Row
				var row = el.parentNode;
				/*while ( ! row.classList.contains('elementor-element') && ! row.classList.contains('elementor-element') ) {
					if ( row.tagName === 'HTML' ) {
						return;
					}
					row = row.parentNode;
				}*/
 
				//row.parentElement.style.overflow = 'hidden';
				row.parentElement.classList.add('image_parallax_row');		
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
		}
		/* mouse hover parallax image*/
		/* mouse scroll parallax image*/
		if(wid_sec.find(".row-parallax-bg-img").length){
			var controller = new ScrollMagic.Controller();
			$('.row-parallax-bg-img').each(function(index, elem){
				var tween = 'tween1-'+index;
				tween = new TimelineMax();
				var lengthBox = $(elem).find('.parallax-bg-img').length;
 
				var $bcg =  $(elem).find('.parallax-bg-img');
 
				var slideParallaxScene = new ScrollMagic.Scene({
					triggerElement: elem,
					triggerHook: 1,
					duration: "200%"
				})
				.setTween(TweenMax.fromTo($bcg, 1, {delay:0.5,backgroundPositionY: '20%', ease:Power0.easeNone},{delay:0.5,backgroundPositionY: '80%', ease:Power0.easeNone}))
				.addTo(controller);
			})
		}
		/* mouse scroll parallax image*/
		if(wid_sec.find(".row-animated-bg").length){
			$(".row-animated-bg").each(function() {
				var data_id= $(this).data('id');
				var data_time=$(this).data('bg-time');
				var colors =$(this).data('bg');
				$('.'+data_id).animatedBG({
					colorSet: colors,
					speed: data_time
				});
			});
		}
		/* mouse scroll parallax image*/
		if(wid_sec.find(".columns-video-bg video, .columns-video-bg .columns-bg-frame").length){
			setTimeout(function(){
			$('.columns-video-bg video, .columns-video-bg .columns-bg-frame').pt_plus_VideoBgInit();
				$('.self-hosted-videos').each(function() {
					var $self=$(this);
                    const promise = $self[0].play();
                    if(promise !== undefined){
                        promise.then(() => {
                        }).catch(() => {
                            $self[0].muted = true;
                            $self[0].play()
                        });
                    }
				});
 
			}, 100);
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
		}
 
		//self hosted video
		if( $(".pt-plus-row-set .pt-plus-bg-video .self-hosted-videos").length > 0 ){
			$(".pt-plus-row-set .pt-plus-bg-video .self-hosted-videos").each(function() {
				var inner_width=window.innerWidth;
				var dk_mp4=$(this).data("dk-mp4");
				var dk_webm=$(this).data("dk-webm");
				var tb_mp4=$(this).data("tb-mp4");
				var mb_mp4=$(this).data("mb-mp4");
				if(inner_width <=767 && mb_mp4!=undefined && mb_mp4!=''){
					var mp4_video='<source src="'+mb_mp4+'" type="video/mp4">';
					$(this).append(mp4_video);					
				}else if(inner_width >=768 && inner_width <=1024 && tb_mp4!=undefined && tb_mp4!=''){
					var mp4_video='<source src="'+tb_mp4+'" type="video/mp4">';
					$(this).append(mp4_video);					
				}else{
					if(dk_mp4!=undefined && dk_mp4!=''){
						var mp4_video='<source src="'+dk_mp4+'" type="video/mp4">';
						$(this).append(mp4_video);
					}
					if(dk_webm!=undefined && dk_webm!=''){
						var webm_video='<source src="'+dk_webm+'" type="video/webm">';
						$(this).append(webm_video);
					}
				}
			});
		}
 
		//Youtube Video
		if(wid_sec.find(".columns-youtube-bg").length){
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
							   },
							   onStateChange: function(e) {
									if(e && e.data === 1){
										var videoHolder = document.getElementById('wrapper-'+id);
										if(videoHolder && videoHolder.id){
											videoHolder.classList.remove('tp-loading');
										}
									}else if(e && e.data === 0){
										e.target.playVideo()
									}
								}
							},
 
						});
 
				});
			};
		}
 
		//Vimeo Video
		if(wid_sec.find(".columns-vimeo-bg").length){
			$(document).ready(function() {
				$('.columns-vimeo-bg iframe').each(function() {
					var $self = $(this);
 
					if (window.addEventListener) {
						window.addEventListener('message', onMessageReceived, false);
					} else {
						window.attachEvent('onmessage', onMessageReceived, false);
					}
 
					function onMessageReceived(e) {
						if(e.origin==='https://player.vimeo.com'){
							var data = JSON.parse(e.data),
							id = $self.attr('id');
							switch (data.event) {
								case 'ready':
									$self[0].contentWindow.postMessage('{"method":"play", "value":1}','https://player.vimeo.com');
									if($self.data('muted') && $self.data('muted') == '1') {
										$self[0].contentWindow.postMessage('{"method":"setVolume", "value":0}','https://player.vimeo.com');
									}
									var videoHolder = document.getElementById('wrapper-'+id);
									if(videoHolder && videoHolder.id){
										videoHolder.classList.remove('tp-loading');
									}
									break;
							}
						}
					}
				});
			});
		}
 
		/*--canvas style 2--*/
		if(wid_sec.find(".pt-plus-row-canvas-style-2").length){
			$(document).ready(function() {
				if ($(".pt-plus-row-canvas-style-2").length) {
					$('.pt-plus-row-canvas-style-2').each(function() {
						var $self = $(this);
						var can2_color =$self.attr('data-color');
						var canid=$self.attr('id');
						particlesJS(canid,{particles:{number:{value:80,density:{enable:!0,value_area:800}},color:{value:can2_color},shape:{type:"circle",stroke:{width:0,color:"#000000"},polygon:{nb_sides:5},image:{src:"img/github.svg",width:100,height:100}},opacity:{value:.5,random:!1,anim:{enable:!1,speed:1,opacity_min:.1,sync:!1}},size:{value:2,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!0,distance:150,color:can2_color,opacity:.4,width:1},move:{enable:!0,speed:2,direction:"none",random:!1,straight:!1,out_mode:"out",bounce:!1,attract:{enable:!1,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:!0,mode:"grab"},onclick:{enable:!0,mode:"push"},resize:!0},modes:{grab:{distance:150,line_linked:{opacity:1}},bubble:{distance:400,size:40,duration:2,opacity:8,speed:3},repulse:{distance:200,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
					});					
				}
			});
		}
		/*canvas style 2--*/
		/*--canvas style 3--*/
		if(wid_sec.find(".canvas-style-3").length){
			$(document).ready(function() {
				if ($(".canvas-style-3").length) {
					$('.canvas-style-3').each(function() {
						var $self = $(this);
						var can3_color =$self.attr('data-color');
						var can3_type=$self.attr('data-type');
						var canid=$self.attr('id');
						particlesJS(canid, {"particles":{"number":{"value":80,"density":{"enable":true,"value_area":800}},"color":{"value":can3_color},"shape":{"type":can3_type,"stroke":{"width":4,"color":can3_color},"polygon":{"nb_sides":8},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":2,"random":true,"anim":{"enable":false,"speed":102.321728,"size_min":25.174393,"sync":true}},"line_linked":{"enable":true,"distance":150,"color":can3_color,"opacity":0.4,"width":1},"move":{"enable":true,"speed":6,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":923.076923,"line_linked":{"opacity":1}},"bubble":{"distance":287.712287,"size":40,"duration":3.916083,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
					});				
				}
			});
		}
		/*canvas style 3--*/
		/*--canvas style 4--*/
		if(wid_sec.find(".canvas-style-4").length){
			$(document).ready(function() {
				if ($(".canvas-style-4").length) {
					$('.canvas-style-4').each(function() {
						var $self = $(this);
						var canid=$self.attr('id');
						var can4_color =$self.attr('data-color');
						var can4_type=$self.attr('data-type');
						particlesJS(canid, {"particles":{"number":{"value":10,"density":{"enable":true,"value_area":800}},"color":{"value":can4_color},"shape":{"type":can4_type,"stroke":{"width":0,"color":can4_color},"polygon":{"nb_sides":5},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.505074,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":100.213253,"random":true,"anim":{"enable":true,"speed":10,"size_min":40,"sync":false}},"line_linked":{"enable":false,"distance":481.023618,"color":can4_color,"opacity":1,"width":2},"move":{"enable":true,"speed":8,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"bubble"},"onclick":{"enable":false,"mode":"push"},"resize":true},"modes":{"grab":{"distance":431.568431,"line_linked":{"opacity":0.364281}},"bubble":{"distance":263.73626373626377,"size":55.944055,"duration":2.157842,"opacity":0.335664,"speed":3},"repulse":{"distance":239.760239,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
					});				
				}
			});
		}
		/*canvas style 4--*/
		/*--canvas style 5--*/
		if(wid_sec.find(".pt-plus-row-canvas-style-5").length){
			$(document).ready(function() {
				if ($(".pt-plus-row-canvas-style-5").length) {
					$('.pt-plus-row-canvas-style-5').each(function() {
						var $self = $(this);
						var can_color =$self.attr('data-color');
						var canid=$self.attr('id');
						particlesJS(canid,{particles:{number:{value:600,density:{enable:!0,value_area:800}},color:{value:can_color},shape:{type:"circle",stroke:{width:0,color:"#000000"},polygon:{nb_sides:5},image:{src:"",width:100,height:100}},opacity:{value:0,random:!1,anim:{enable:!1,speed:0,opacity_min:0,sync:!1}},size:{value:3,random:!0,anim:{enable:!1,speed:40,size_min:.1,sync:!1}},line_linked:{enable:!0,distance:32.068241,color:can_color,opacity:.8,width:1},move:{enable:!0,speed:4,direction:"none",random:!1,straight:!1,out_mode:"out",bounce:!1,attract:{enable:!1,rotateX:600,rotateY:1200}}},interactivity:{detect_on:"canvas",events:{onhover:{enable:true,mode:"repulse"},onclick:{enable:!1,mode:"push"},resize:!0},modes:{grab:{distance:400,line_linked:{opacity:1}},bubble:{distance:200,size:140,duration:2,opacity:8,speed:2},repulse:{distance:100,duration:.4},push:{particles_nb:4},remove:{particles_nb:2}}},retina_detect:!0});
					});				
				}
			});
		}
		/*canvas style 5--*/
		/*--canvas style 6--*/
		if(wid_sec.find(".canvas-style-6").length){
			$(document).ready(function() {
				$('.canvas-style-6').each(function() {
					var $self = $(this);
					var cancolor=$self.attr("data-canvas-color");
					$($self).particleground({
						  minSpeedX: 0.1,
						  maxSpeedX: 0.3,
						  minSpeedY: 0.1,
						  maxSpeedY: 0.3,
						  directionX: "center",
						  directionY: "up",
						  density: 10000,
						  dotColor: cancolor,
						  lineColor: cancolor,
						  particleRadius: 7,
						  lineWidth: 1,
						  curvedLines: false,
						  proximity: 100,
						  parallax: true,
						  parallaxMultiplier: 5,
						  onInit: function() {},
						  onDestroy: function() {}
					});
				});				
			});
		}
		/*canvas style 6--*/
		/*--canvas style 7--*/
		if(wid_sec.find(".canvas-style-7").length){
			$(document).ready(function() {
				if ($(".canvas-style-7").length) {
					$('.canvas-style-7').each(function() {
						var $self = $(this);
						var can7_color =$self.attr('data-color');
						var can7_type=$self.attr('data-type');
						var canid=$self.attr('id');
						particlesJS(canid, {"particles":{"number":{"value":400,"density":{"enable":true,"value_area":2840.9315098761817}},"color":{"value":can7_color},"shape":{"type":can7_type,"stroke":{"width":0,"color":can7_color},"polygon":{"nb_sides":5},"image":{"src":"","width":100,"height":100}},"opacity":{"value":0.5,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":11,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":224.4776885211732,"color":can7_color,"opacity":0.1683582663908799,"width":1.2827296486924182},"move":{"enable":true,"speed":3,"direction":"bottom","random":true,"straight":false,"out_mode":"bounce","bounce":false,"attract":{"enable":false,"rotateX":881.8766334760375,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"bubble"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":0.5}},"bubble":{"distance":400,"size":4,"duration":0.3,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
					});
 
				}
			});
		}
		/*canvas style 7--*/
 
		/*------------- parallax images js ---*/
		if(wid_sec.find(".parallax_image").length){
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
		}
		/*------------- parallax images js ---*/
 
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-row-background.default', WidgetRowBackgroundHandler);
		if (elementorFrontend.isEditMode()) {
			elementorFrontend.hooks.addAction('frontend/element_ready/tp-row-background.default', WidgetRowBackgroundHandler);
		}
	});
})(jQuery);
 
/*carousel background connection tabs/accordion*/
function background_accordion_tabs_conn(index,conn_id){
	var $= jQuery;
	if($("#"+conn_id).length > 0 && index){
		var active_bg = $("#"+conn_id+' .bg-carousel-slide');
		active_bg.removeClass('bg-active-slide');
		if(active_bg.length >= index){
			active_bg.eq(index-1).addClass('bg-active-slide');
		}else{
			var bg_total = active_bg.length;
			var c = index % bg_total;
			active_bg.eq(c-1).addClass('bg-active-slide');
		}
	}
}
/*carousel background connection tabs/accordion*/
 
/*----mordern parallax image----*/
(function($) {
'use strict';
	$(window).on("resize",function() {
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
/*-----mordern parallax image---*/
/*video bg responsive*/
(function($) {
    'use strict';
	var d_i=0,t_i=0,m_i=0;
	$(window).on('load resize',function() {
		var inner_width=window.innerWidth;
		if($('body').find(".plus-video-poster").length>0){
			$('.plus-video-poster').each(function(){
				var desktop_poster=$(this).data("desktop-poster"),tablet_poster=$(this).data("tablet-poster"),mobile_poster=$(this).data("mobile-poster");				
				if(tablet_poster==undefined || tablet_poster==''){
					tablet_poster=desktop_poster;
				}
				if(mobile_poster==undefined || mobile_poster==''){
					mobile_poster=tablet_poster;
				}
				if(inner_width<=1024 && inner_width>=768 && tablet_poster!=undefined && t_i==0){
					if(!$(this).hasClass("self-hosted-video-bg")){
						$(this).css('background-image', 'url(' + tablet_poster + ')');
					}else{
						$(this).css('background-image', 'url(' + tablet_poster + ')');
						$('video',this).attr('poster',tablet_poster);
					}
					d_i=0;t_i++;m_i=0;
				}
				if(inner_width<=767 && mobile_poster!=undefined && m_i==0){
					if(!$(this).hasClass("self-hosted-video-bg")){
						$(this).css('background-image', 'url(' + mobile_poster + ')');
					}else{
						$(this).css('background-image', 'url(' + mobile_poster + ')');
						$('video',this).attr('poster',mobile_poster);
					}
					d_i=0;t_i=0;m_i++;
				}
				if(inner_width>=1025 && desktop_poster!=undefined && d_i==0){
					if(!$(this).hasClass("self-hosted-video-bg")){
						$(this).css('background-image', 'url(' + desktop_poster + ')');
					}else{
						$(this).css('background-image', 'url(' + desktop_poster + ')');
						$('video',this).attr('poster',desktop_poster);
					}
					d_i++;t_i=0;m_i=0;
				}
			});
		}
	});	
})(jQuery);
/*video bg responsive*/