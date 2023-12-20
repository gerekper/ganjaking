(function ($) {
	"use strict";
	var WidgetMultiScrollHandler = function ($scope, $) {
		var container = $scope[0].querySelectorAll('.tp-horizontal-scroll-wrapper'),
			rsetting = (container[0] && container[0].dataset) ? JSON.parse(container[0].dataset.result) : [],
			id = (rsetting && rsetting.id) ? rsetting.id : '',
			tempID = (rsetting && rsetting.tempID) ? rsetting.tempID : '',
			distanceLastslide = (rsetting && rsetting.distanceLastslide) ? rsetting.distanceLastslide : 0,
			bg_transition = (rsetting && rsetting.bg_transition) ? 1 : 0,
			opacity_scroll = (rsetting && rsetting.opacity_scroll) ? 1 : 0,
			opacityVal = (rsetting && rsetting.opacityVal) ? rsetting.opacityVal : 0,
			responsive = (rsetting && rsetting.responsive) ? 1 : 0,
			responsiveWidth = (rsetting && responsive && rsetting.responsiveWidth) ? rsetting.responsiveWidth : '',
			customWidth = (rsetting && rsetting.customWidth) ? rsetting.customWidth.split('|') : [],
			rtl_scroll = (rsetting && rsetting.rtl_scroll) ? 1 : 0,
			tp_fullscroll = (rsetting && rsetting.tp_fullscroll) ? 1 : 0,
			scroll_effects = (rsetting && rsetting.scroll_effects) ? rsetting.scroll_effects : 'normal',
			scroll_skew_val = (rsetting && rsetting.scroll_skew_val) ? rsetting.scroll_skew_val : 5,
			scroll_scale_val = (rsetting && rsetting.scroll_scale_val) ? rsetting.scroll_scale_val : 0.9,
			section_id = (rsetting && rsetting.section_id) ? rsetting.section_id.split('|') : [],
			scrollSpeed = (rsetting && rsetting.speed) ? rsetting.speed : 4,
			URLParameter = (rsetting && rsetting.URLParameter) ? 1 : 0,
			horizontal_unique_id = (rsetting && rsetting.horizontal_unique_id) ? rsetting.horizontal_unique_id : '',
			tpScrollTrigger = `.tp-horizontal-scroll-wrapper-${id} .elementor`,
			scrollTrigger = '',
			deviceMode = document.querySelector('body').dataset.elementorDeviceMode,
			fixBg = container[0].parentElement.querySelectorAll('.tp-fixbg');
		
			if(deviceMode == 'tablet'){
				customWidth = (rsetting && rsetting.customWidthTab) ? rsetting.customWidthTab.split('|') : customWidth
				scrollSpeed = (rsetting && rsetting.SpeedTab) ? rsetting.SpeedTab : scrollSpeed
				distanceLastslide = (rsetting && rsetting.DistlastslideTab) ? rsetting.DistlastslideTab : distanceLastslide
			}else if(deviceMode == 'mobile'){
				customWidth = (rsetting && rsetting.customWidthMob) ? rsetting.customWidthMob.split('|') : customWidth
				scrollSpeed = (rsetting && rsetting.SpeedTab) ? rsetting.SpeedMob : scrollSpeed
				distanceLastslide = (rsetting && rsetting.DistlastslideMob) ? rsetting.DistlastslideMob : distanceLastslide
			}
			
			gsap.registerPlugin(ScrollTrigger);
			tp_section_remove(deviceMode)
			
			ScrollTrigger.config({
				limitCallbacks: true,
				ignoreMobileResize: true
			})

        var getSec = `.tp-horizontal-scroll-wrapper-${id} .elementor-${tempID} > .e-con, .tp-horizontal-scroll-wrapper-${id} .elementor-${tempID} > .elementor-section, .tp-horizontal-scroll-wrapper-${id} .elementor-${tempID} > .elementor-inner > .elementor-section-wrap > .elementor-section, .tp-horizontal-scroll-wrapper-${id} .elementor-${tempID} > .elementor-inner > .elementor-section-wrap > .e-con`,
			getSections = gsap.utils.toArray(getSec),
			parentclass = container[0].querySelectorAll('.elementor, .elementor > .elementor-inner > .elementor-section-wrap'),
			secLength = getSections.length,
			getScrollWidth=	window.innerWidth,
			scrollVal = '',
			snapEffect = false,
			activeSection = 0,
			scrollTimeout;

		/**responsive enable diable */
		function responsiveDisable(){
			if (responsive && screen.width <= responsiveWidth) {
				container[0].classList.add('responsive');

				if (parentclass.length > 0) {
					parentclass[0].style.cssText = 'width:unset;display:block';
				}
			}else{
				parentclass[0] ? parentclass[0].style.cssText = 'display:flex' : '';
				parentclass[1] ? parentclass[1].style.cssText = 'display:flex' : '';

				let totalWith = 0;

				customWidth.forEach(function(value,index) {
					if(customWidth.length == 1){
						totalWith = customWidth[0] * secLength;
					}else{
						totalWith += Number(value);
					}
				});

				getSections.forEach(function(self,index){

					let secWidth = customWidth[index]
					if(customWidth.length == 1){
						secWidth = customWidth[0];
					}

					if(index > customWidth.length - 1){
						secWidth = customWidth[0];
					}

					self.style.display = 'flex';
					self.style.width = secWidth + 'vw';

				});

				parentclass.length > 0 ? parentclass[0].style.width = `${totalWith}vw` : '';
				tpHorizontalScroll(scrollTrigger)
			}
		}

		/**Snap Effect */
		if (tp_fullscroll) {
			snapEffect = 1 / (secLength - 1)
		}
		
		/**Add Section ID */
		if (section_id.length > 0 && secLength > 0) {
			getSections.forEach(function (self, index) {
				self.setAttribute('id', section_id[index]);
			})
		}
		
		/**Background Transition */
		if (bg_transition) {
			let bg_div = container[0].querySelector(`.tp-bg-hscroll-${id}`).outerHTML;

				container[0].querySelector(`.tp-horizontal-scroll-wrapper > .tp-bg-hscroll-${id}`).remove();
				parentclass.length > 0 ? parentclass[0].insertAdjacentHTML("afterbegin", bg_div) : '';

				var tpClosest = container[0].closest('.e-con, .elementor-section'),
					getBgSection = tpClosest.querySelectorAll(`.tp-bg-hscroll-${id} .bg-scroll`);
					
					getBgSection.length > 0 ? getBgSection[0].classList.add('active') : '';
		}

		/**Fix Content */
		if(fixBg.length > 0){
			let fixConHtml = fixBg[0].outerHTML;
				parentclass.length > 0 ? parentclass[0].insertAdjacentHTML("afterbegin", fixConHtml) : '';
				fixBg[0].remove()
		}

		getScrollWidth = getScrollWidth * scrollSpeed
		let addScrollVal = 1 - (100 / customWidth)
			addScrollVal = Math.trunc(addScrollVal*100)
		
		let getSvgDiv = document.querySelectorAll('.pt_plus_animated_svg')
			getSvgDiv.forEach(function(self){
				self.style.display = 'none';
			});

		let isActive = 0;
		function tpTriggeronOff(isActive){
			let getTrigger = document.querySelectorAll('.gsap-marker-scroller-end,.gsap-marker-scroller-start ,.gsap-marker-end,.gsap-marker-start')

				getTrigger.forEach(function(trigg){
					if(isActive){
						trigg.style.display = 'block'
					}else{
						trigg.style.display = 'none'
					}
				});
		}

		setTimeout(() => {
			tpTriggeronOff(isActive)
		}, 500);

		var headinAni = container[0].querySelectorAll('.heading_style.style-10');

		function tpHorizontalScroll(scrollTrigger){

			/**RTL Scroll */
			if (rtl_scroll) {
				setTimeout(() => {
					let getPaginate = container[0].querySelectorAll('.tp-hscroll-pagination'),
						getCarousal = container[0].querySelectorAll('.cr-horizontal-scroll'),
						tpCarousalRemote = container[0].querySelectorAll('.theplus-carousel-remote')

						getCarousal.length > 0 ? getCarousal[0].style.left = '0px' : ''
						tpCarousalRemote.length > 0 ? tpCarousalRemote[0].style.direction = 'ltr' : ''
						getPaginate && getPaginate.length > 0 ? getPaginate[0].style.direction = 'rtl' : ''

						parentclass.length > 0 ? parentclass[0].style.direction = 'rtl' : ''
						getSections.forEach(function(self){
							let secWidth = getSections[0].clientWidth
							let getMarginVal = parentclass[0].clientWidth - window.innerWidth
								self.style.left = `-${getMarginVal}px`
						})
				}, 100);
				scrollVal = parentclass[0].clientWidth - window.innerWidth + distanceLastslide
			}else{
				let totWidth = 0;
				getSections.forEach(function(self){
					totWidth += Number(self.clientWidth)
				});

				scrollVal = -(totWidth - window.innerWidth) - distanceLastslide;
			}

			scrollTrigger = gsap.to(getSections, {
				x: scrollVal,
				ease: "none",
				scrollTrigger: {
					trigger: tpScrollTrigger,
					pin: true,
					scrub: 1,
					snap: snapEffect,
					end: `+=${getScrollWidth}`,
					onUpdate: (self) => {
						var paginationCon = container[0].querySelectorAll('.hscroll-pagination-slides .hs-current-slides'),
							newDirection = self.direction,
							progressCon = container[0].querySelectorAll('.tp-horizontal-scroll-progress-bar'),
							progressTooltip = progressCon.length > 0 ? progressCon[0].querySelector('.tp-progress-tooltip') : '',
							progressbar = Math.floor(self.progress * 100),
							pbClasslist = progressCon.length > 0 ? progressCon[0].classList : '',
							navDots = container[0].querySelectorAll('.tp-carousel-dots .tp-carodots-item'),
							getCurrentDiv = getSections[activeSection];
	
						if( getCurrentDiv ){
							var getSvgani = getCurrentDiv.querySelectorAll('.pt_plus_animated_svg'),
								animate = getCurrentDiv.querySelectorAll('.animate-general'),
								previousSection = activeSection - 1,
								substyle = getCurrentDiv.querySelectorAll('.sub-style');
	
								activeSection = Math.round(self.progress * (secLength - 1));
	
							if (!getCurrentDiv.classList.contains('tp-heading-ani-called') && headinAni.length > 0 && substyle.length > 0) {
								substyle.forEach(function(self){
									var animsplitType = self.dataset.animsplitType,
										attr = self.dataset.aniattrht,
										attr = JSON.parse(attr),
										animation = Power4.easeOut;
			
										if(attr && attr["effect"] != undefined && attr["effect"] != 'default'){                        
											animation = attr['effect'];
										}
			
									let mySplitText = new SplitText(self, { type: animsplitType }),
										splitTextTimeline = new TimelineLite();
									
									headingAnimation(self, animation, attr, splitTextTimeline, mySplitText);
									getCurrentDiv.classList.add('tp-heading-ani-called');
								})
							}
	
							if(scroll_effects != 'normal' && parentclass.length > 0){
	
								if (self.progress < 0.90) {
									parentclass[0].style.transition = 'all 0.5s ease'
								}
	
								if (newDirection > 0) {
									scrollEffects('positive')
								}else if (newDirection < 0) {
									scrollEffects('nagitive')
								}
	
								clearTimeout(scrollTimeout);
								scrollTimeout = setTimeout(() => {
									gsap.to(parentclass[0], { skewX: 0, scaleY: 1, duration: 0.5 });
								}, 300);
							}

							svgAnimation(getSvgani,getCurrentDiv)
							tpAnimation(animate,getCurrentDiv,previousSection)
							opacityScroll(getSections)
							bgTransition(getBgSection)
							carouselRemote(paginationCon,progressTooltip,progressbar,progressCon,pbClasslist,navDots)
	
							container[0].setAttribute('data-active-slide', activeSection);
						}
					},
					onEnter: () => {
						isActive = 1;
						tpTriggeronOff(isActive)
						scroll_effects != 'normal' ? clearTimeout(scrollTimeout) : '';
					},
					onEnterBack: () => {
						isActive = 1;
						tpTriggeronOff(isActive)
					},
					onLeaveBack: () => {
						isActive = 0;
						tpTriggeronOff(isActive)
						if(scroll_effects != 'normal' && parentclass.length > 0){
							gsap.to(parentclass[0], { skewX: 0, scaleY: 1, duration: 0.5 });
							clearTimeout(scrollTimeout);
						}
					},
					onLeave: () => {
						isActive = 0;
						tpTriggeronOff(isActive)
						clearTimeout(scrollTimeout);
						parentclass.length > 0 ? parentclass[0].style.transition = 'unset' : ''
						if(scroll_effects != 'normal' && parentclass.length > 0){
							gsap.to(parentclass[0], { skewX: 0, scaleY: 1, duration: 0.5 });
						}
					},
					direction: "Horizontal",
				}
			});

			let getele = container[0].querySelectorAll('.tp-gsap-scroll'),
				gsapAttr = {},
				triNum = 0;

				getele.forEach(function(ele,index){
					if(ele.dataset && ele.dataset.tpaeHs){
						gsapAttr = JSON.parse(ele.dataset.tpaeHs)                    
					}
					let redData = ele.dataset && ele.dataset.tpaeMsview ? JSON.parse(ele.dataset.tpaeMsview) : [],
						hsWidth = redData && redData.HSwidth ? redData.HSwidth : 0,
						resWidth = hsWidth && redData && redData.resWidth ? redData.resWidth : '';


					if( gsapAttr.length > 0 || screen.width > resWidth){
						gsapAttr.forEach(function(attr,idx){
							triNum++;
							var getDevice = 'desktop';

							if(deviceMode == 'tablet'){
								getDevice = 'tablet'
							}else if(deviceMode == 'mobile'){
								getDevice = 'mobile'
							}
		
							var borStart = 0, 
								borEnd = 0,
								tpProperties = {},
								tpPropertiesEnd = {};
							
							
							if( attr ){
								var hsdeveloptp = attr.developer ? attr.developer[0] : 0,   
									hsdevNametp = attr.developer && attr.developer[1] ? attr.developer[1] : triNum,   
									trigStart = attr.animation && attr.animation.trigger ? attr.animation.trigger[getDevice][0] : 0.5, 
									trigEnd = attr.animation && attr.animation.trigger ? attr.animation.trigger[getDevice][1] : 0.4,
									scrollStart = attr.animation && attr.animation.scroll ? attr.animation.scroll[getDevice][0] : 0.8,
									scrollEnd = attr.animation && attr.animation.scroll ? attr.animation.scroll[getDevice][1] : 0.2;

								if(attr.bgColor && attr.bgColor.background){
									var bgColorStart = attr.bgColor ? attr.bgColor[getDevice][0] : '',
										bgColorEnd = attr.bgColor ? attr.bgColor[getDevice][1] : '';

									tpProperties.backgroundColor = bgColorStart;
									tpPropertiesEnd.backgroundColor = bgColorEnd;
								}

								if(attr.vertical && attr.vertical.verticalY){
									var verticalStart = attr.vertical[getDevice][0] ? attr.vertical[getDevice][0] : 0,
										verticalEnd = attr.vertical[getDevice][1] ? attr.vertical[getDevice][1] : 0;
									
									tpProperties.y = verticalStart;
									tpPropertiesEnd.y = verticalEnd;
								}

								if(attr.horizontal && attr.horizontal.horizontalX){
									var horiStart = attr.horizontal[getDevice][0] ? attr.horizontal[getDevice][0] : 0,
										horiEnd = attr.horizontal[getDevice][1] ? attr.horizontal[getDevice][1] : 0;

									tpProperties.x = horiStart;
									tpPropertiesEnd.x = horiEnd;
								}

								if(attr.opacity && attr.opacity.opacity){
									var opacityStart = attr.opacity[getDevice][0] ? attr.opacity[getDevice][0] : 0,
										opacityEnd = attr.opacity[getDevice][1] ? attr.opacity[getDevice][1] : 0;
									
									tpProperties.opacity = opacityStart;
									tpPropertiesEnd.opacity = opacityEnd;
								}

								if(attr.rotate && attr.rotate.rotate){
									var rotateXstart = attr.rotate.rotateX && attr.rotate.rotateX[getDevice][0] ? attr.rotate.rotateX[getDevice][0] : 0,
										rotateXend = attr.rotate.rotateX && attr.rotate.rotateX[getDevice][1] ? attr.rotate.rotateX[getDevice][1] : 0,
										rotateYstart = attr.rotate.rotateY && attr.rotate.rotateY[getDevice][0] ? attr.rotate.rotateY[getDevice][0] : 0,
										rotateYend = attr.rotate.rotateY && attr.rotate.rotateY[getDevice][1] ? attr.rotate.rotateY[getDevice][1] : 0,
										rotateZstart = attr.rotate.rotateZ && attr.rotate.rotateZ[getDevice][0] ? attr.rotate.rotateZ[getDevice][0] : 0,
										rotateZend = attr.rotate.rotateZ && attr.rotate.rotateZ[getDevice][1] ? attr.rotate.rotateZ[getDevice][1] : 0,
										rotatePosi = attr.rotate.position ? attr.rotate.position : 'center center';

									if(attr.rotate.rotateX){
										tpProperties.rotationX = rotateXstart
										tpPropertiesEnd.rotationX = rotateXend
									}
									if(attr.rotate.rotateY){
										tpProperties.rotationY = rotateYstart
										tpPropertiesEnd.rotationY = rotateYend
									}
									if(attr.rotate.rotateZ){;
										tpProperties.rotationZ = rotateZstart
										tpPropertiesEnd.rotationZ = rotateZend
									}

									attr.rotate.position ? tpPropertiesEnd.transformOrigin = rotatePosi : {};
								}

								if(attr.scale && attr.scale.scale){
									var scaleXstart = attr.scale.scaleX && attr.scale.scaleX[getDevice][0] ? attr.scale.scaleX[getDevice][0] : 0,
										scaleXEnd = attr.scale.scaleX && attr.scale.scaleX[getDevice][1] ? attr.scale.scaleX[getDevice][1] : 0,
										scaleYstart = attr.scale.scaleY && attr.scale.scaleY[getDevice][0] ? attr.scale.scaleY[getDevice][0] : 0,
										scaleYEnd = attr.scale.scaleY && attr.scale.scaleY[getDevice][1] ? attr.scale.scaleY[getDevice][1] : 0,
										scaleZstart = attr.scale.scaleZ && attr.scale.scaleZ[getDevice][0] ? attr.scale.scaleZ[getDevice][0] : 0,
										scaleZEnd = attr.scale.scaleZ && attr.scale.scaleZ[getDevice][1] ? attr.scale.scaleZ[getDevice][1] : 0;

									if(attr.scale.scaleX){
										tpProperties.scaleX = scaleXstart
										tpPropertiesEnd.scaleX = scaleXEnd
									}
									if(attr.scale.scaleY){
										tpProperties.scaleY = scaleYstart
										tpPropertiesEnd.scaleY = scaleYEnd
									}
									if(attr.scale.scaleZ){
										tpProperties.scaleZ = scaleZstart
										tpPropertiesEnd.scaleZ = scaleZEnd
									}
								}

								if(attr.skew && attr.skew.skew){
									var skewXstart = attr.skew && attr.skew.skewX && attr.skew.skewX[getDevice][0] ? attr.skew.skewX[getDevice][0] : 0,
										skewXEnd = attr.skew && attr.skew.skewX && attr.skew.skewX[getDevice][1] ? attr.skew.skewX[getDevice][1] : 0,
										skewYstart = attr.skew && attr.skew.skewY && attr.skew.skewY[getDevice][0] ? attr.skew.skewY[getDevice][0] : 0,
										skewYEnd = attr.skew && attr.skew.skewY && attr.skew.skewY[getDevice][1] ? attr.skew.skewY[getDevice][1] : 0;

									if(attr.skew.skewX){
										tpProperties.skewX = skewXstart
										tpPropertiesEnd.skewX = skewXEnd
									}
									if(attr.skew.skewY){
										tpProperties.skewY = skewYstart
										tpPropertiesEnd.skewY = skewYEnd
									}
								}

								if(attr.border && attr.border.border){
									var borderStart = attr.border[getDevice][0] ? attr.border[getDevice][0] : '',
										bdstunit = borderStart ? borderStart.unit : '',
										borderEnd = attr.border[getDevice][1] ? attr.border[getDevice][1] : '',
										bdetunit = borderEnd ? borderEnd.unit : '';

									if( borderStart ){
										borStart = borderStart.top + bdstunit + ' '
										borStart += borderStart.right +bdstunit + ' '
										borStart += borderStart.bottom + bdstunit + ' '
										borStart += borderStart.left + bdstunit + ' '
									}
				
									if( borderEnd ){
										borEnd = borderEnd.top + bdetunit + ' '
										borEnd += borderEnd.right + bdetunit + ' '
										borEnd += borderEnd.bottom + bdetunit + ' '
										borEnd += borderEnd.left + bdetunit + ' '
									}

									tpProperties.borderRadius = borStart;
									tpPropertiesEnd.borderRadius = borEnd;
								}

							}

							trigStart = trigStart * 100 + '%';
							trigEnd = trigEnd * 100 + '%';
							scrollStart = scrollStart * 100 + '%';
							scrollEnd = scrollEnd * 100 + '%';
						
							var startAni = trigStart + ' ' + scrollStart,
								startEnd = trigEnd + ' ' + scrollEnd;

							if(idx > 0){
								tpProperties = {};
							}
							
							if(!(screen.width <= resWidth)){
								gsap.set(ele, tpProperties);
								gsap.fromTo(ele,
									tpProperties,
									{
										...tpPropertiesEnd,
										ease: "none",
										scrollTrigger: {
											trigger: ele,
											containerAnimation: scrollTrigger,
											start: startAni,
											end: startEnd,
											scrub: 1,
											markers: hsdeveloptp,
											id: hsdevNametp,
										},
									}
								);
							}
						})
					}
					
				})
		}

		responsiveDisable()

		setTimeout(() => {
			
			var prevBtn = container[0].querySelectorAll('.nav-prev-slide'),
				nextBtn = container[0].querySelectorAll('.nav-next-slide');

			if (rtl_scroll) {
				prevBtn.length > 0 ? prevBtn[0].addEventListener('click', nextSection) : '';
				nextBtn.length > 0 ? nextBtn[0].addEventListener('click', prevSection) : '';
			}else{
				prevBtn.length > 0 ? prevBtn[0].addEventListener('click', prevSection) : '';
				nextBtn.length > 0 ? nextBtn[0].addEventListener('click', nextSection) : '';
			}

			function nextSection(event){
				goToNextSection(event,activeSection,getSections,URLParameter);
			}

			function prevSection(event){
				goToPrevSection(event,activeSection,getSections,URLParameter)
			}

			navDots(container,URLParameter)

		}, 50);

		function tpnavDots(){
			let getID = container[0].querySelectorAll("a");
			getID.forEach(element => {
				if (element.hash) {
					element.addEventListener('click', (e) => {

						e.stopPropagation();
						e.preventDefault();
						e.stopImmediatePropagation();

						let gettriger = '';
						if (e.target.tagName !== 'A') {
							gettriger = e.target.closest('a');
						} else {
							gettriger = e.target;
						}
						
						var Hashvalue = element.hash;
						setTimeout(() => {
							if (URLParameter) {
								document.location.hash = Hashvalue;
							}
						}, 100);

						let FindSection = document.querySelectorAll(`${gettriger.hash}`);
							if (FindSection.length > 0) {
								let getdata = FindSection[0].dataset.scrollOffset;
								tpScrollTo(getdata)
							}
					});
					if (URLParameter) {
						setTimeout(() => {
							let getHashlink = document.location.hash ? document.querySelector(`${document.location.hash}`) : '',
								getdata = getHashlink ? getHashlink.dataset.scrollOffset : ''
							tpScrollTo(getdata)
						}, 100);
					}
				}
			});
		}
		tpnavDots();

		function tpAnimation(animate,getCurrentDiv,previousSection){
			if(animate.length > 0){
				jQuery(getCurrentDiv).find('.animate-general:not(.animation-done)').each(function () {
					var d,
						b = $(this),
						delay_time = b.data("animate-delay"),
						d = b.data("animate-type"),
						o = b.data("animate-out-type");
					if (b.hasClass("animation-done")) {
						b.hasClass("animation-done");
					} else {
						b.addClass("animation-done").velocity(d, { delay: delay_time, display: 'auto' });
						if (previousSection >= 0) {
							var previousDiv = getSections[previousSection],
								b = jQuery(previousDiv).find('.animate-general'); 
							b.addClass("animation-out-done").removeClass("animation-done").velocity(o, { delay: delay_time, display: 'auto' });
						}
					}
				});
			}
		}

		function bgTransition(getBgSection){
			if (getBgSection && getBgSection.length > 0) {
				getBgSection.forEach((section, index) => {
					if (index < activeSection) {
						section.classList.remove('active')
					} else if (index == activeSection) {
						section.classList.add('active')
					} else {
						section.classList.remove('active')
					}
				});
			}
		}

		function opacityScroll(getSections){
			if (secLength > 0) {
				getSections.forEach((section, index) => {
					if (opacity_scroll) {
						if (index < activeSection) {
							gsap.to(section, { opacity: 1, duration: 0.5 });
						} else if (index == activeSection) {
							gsap.to(section, { opacity: 1, duration: 0.5 });
						} else {
							gsap.to(section, { opacity: opacityVal, duration: 0.5 });
						}
					}
				});
			}
		}

		function carouselRemote(paginationCon, progressTooltip, progressbar, progressCon, pbClasslist, navDots){

			/**Pagination */
			if (paginationCon.length > 0) {
				paginationCon.forEach(function(section,index){
					if (index < activeSection) {
						section.classList.remove('active')
						section.classList.remove('animated')
					} else if (index == activeSection) {
						section.classList.add('active')
						section.classList.add('animated')
					} else {
						section.classList.remove('active')
						section.classList.remove('animated')
					}
				})
			}

			/**Progress Bar */
			if (progressCon.length > 0) {
				if (pbClasslist.contains('horizontal')) {
					progressCon[0].style.width = progressbar + '%';
				}else if (pbClasslist.contains('vertical')){
					progressCon[0].style.height = progressbar + '%';
				}
				if(pbClasslist.contains('style-2')){
					progressTooltip.innerHTML = progressbar + '%';
				}
				if(pbClasslist.contains('style-3')){
					progressCon[0].innerHTML = progressbar + '%';
				}
			}

			/**Navigation Dots */
			if(navDots.length > 0){
				navDots.forEach(function(section,index){
					if (index < activeSection) {
						section.classList.remove('active');
						section.classList.add('inactive');
					} else if (index == activeSection) {
						section.classList.add('active');
						section.classList.remove('inactive');
					} else {
						section.classList.remove('active');
						section.classList.add('inactive');
					}
				})
			}
		}

		const tpDataScrollval = () => {
			var offset = jQuery(container[0]).offset().top,
				totalwidth = 0,
				scrollWidth = parentclass.length > 0 ? getSections[0].clientWidth * secLength : '';
				if (secLength > 0) {
					getSections.forEach(function (self, index) {
						var width = self.offsetWidth / scrollSpeed
						totalwidth += (index == 0) ? 0 : width;

					var total = scrollWidth - window.innerWidth,
						percentage = (100 * totalwidth) / (total * 100),
						scrollTop = percentage * (container[0].offsetHeight - window.innerHeight),
						scrollVal = (Number(scrollTop)) * scrollSpeed;

						jQuery(self).attr("data-scroll-offset", scrollVal + offset);
				})
			}
		}
		
		if (!responsive || screen.width >= responsiveWidth ) {
			setTimeout(() => {
				tpDataScrollval();
				URLParametesr(URLParameter);
			}, 100);
		}
		
		function scrollEffects(opration){
			var skewVal = scroll_skew_val;
			if(opration == 'nagitive'){
				skewVal = Number(`-${scroll_skew_val}`);
			}

			if(scroll_effects == 'skew'){
				gsap.to(parentclass[0], { skewX: skewVal, duration: 0.5 });
			} else if (scroll_effects == 'scale'){
				gsap.to(parentclass[0], { scaleY: scroll_scale_val, duration: 0.5 });
			} else if (scroll_effects == 'bounce'){
				gsap.to(parentclass[0], { skewX: skewVal, scaleY: scroll_scale_val, duration: 0.5 });
			}
		}

		function svgAnimation(getSvgani,getCurrentDiv){
			if (getSvgani.length > 0) {
				if (!getCurrentDiv.classList.contains('tp-animated-svg-called')) {
					jQuery('.pt_plus_animated_svg', getCurrentDiv).css('display','block');
					jQuery('.pt_plus_animated_svg', getCurrentDiv).pt_plus_animated_svg();

					getCurrentDiv.classList.add('tp-animated-svg-called');
				}
			}
		}
		
		function tp_section_remove(deviceMode) {
			var parentclas = container[0].querySelectorAll('.elementor');
			if( parentclas.length > 0 ){
				var section_l = parentclas[0].querySelectorAll('.elementor > .elementor-section, .elementor > .e-con , .elementor > .elementor-inner > .elementor-section-wrap > .elementor-section, .elementor > .elementor-inner > .elementor-section-wrap > .e-con');
				
				if( section_l.length > 0 ){
					section_l.forEach(function(self,idx){
						
						if ( deviceMode == "desktop" ){
							if( self.classList.contains('elementor-hidden-desktop') ){
								tpSecRemove(self,idx)
							}
						} else if ( deviceMode == "tablet" ){
							if( self.classList.contains('elementor-hidden-tablet') ){
								tpSecRemove(self,idx)
							}
						} else if ( deviceMode == "mobile" ){
							if( self.classList.contains('elementor-hidden-mobile') ){
								tpSecRemove(self,idx)
							}
						}
					})
				}

			}
		}

		function tpSecRemove(self, idx){
			self.remove();
			var getDots = container[0].querySelectorAll(`.tp-carodots-item`),
				getBgsec = container[0].querySelectorAll('.bg-scroll')

				if(getDots.length > 0){
					getDots[idx] ? getDots[idx].remove() : '';
				}
				if(getBgsec.length > 0){
					getBgsec[idx] ? getBgsec[idx].remove() : '';
				}
		}

		if( elementorFrontend.isEditMode() ){
			jQuery(`.cr-horizontal-scroll`, $scope).remove();

			var BackendOnly = true,
				getCRData = document.querySelectorAll(`#tptab_${horizontal_unique_id}`);
				getCRData.forEach(function(self){
					var getData = (self && self.dataset && self.dataset.remotedata) ? JSON.parse(self.dataset.remotedata) : '',
						WidgetID = getData ? getData.widgetid : '',
						GetRemote = document.querySelectorAll(`.elementor-element-${WidgetID}`),
						checkHR = document.querySelectorAll(`#tphs_${horizontal_unique_id}`),
						UID = getData ? getData.u_id : '',
						paginate = getData ? getData.paginate : ''

						carousalRemoteAdd(GetRemote,checkHR,BackendOnly,UID,WidgetID,paginate,container[0],getSections,getData)
				})
		}

		function URLParametesr(URLParameter){
			let getHasval = document.location.hash;

			if(getHasval){
				var getScrollDiv = container[0].querySelector(`${getHasval}`),
					getScrollData = getScrollDiv ? getScrollDiv.dataset.scrollOffset : '';
			}
			if(URLParameter){
				gsap.to(window, {
					scrollTo: {
						y: getScrollData,
						autoKill: false
					},
					duration: 1
				});
			}
		}
		
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-horizontal-scroll-advance.default', WidgetMultiScrollHandler);
	});

})(jQuery);

function goToNextSection(event,activeSection,getSections,URLParameter){
	event.stopPropagation();
	event.preventDefault();
	event.stopImmediatePropagation();
	
	let index = Number(activeSection) + 1
	if (index < 0 || index > getSections.length - 1) return;
	activeSection = index;
	
	if(getSections.length > 0){
		prevNextData(activeSection,getSections,URLParameter)
	}
}

function goToPrevSection(event,activeSection,getSections,URLParameter){
	event.stopPropagation();
	event.preventDefault();
	event.stopImmediatePropagation();

	let index = Number(activeSection) - 1
	if (index < 0 || index > getSections.length - 1) return;
	activeSection = index;

	if(getSections.length > 0){
		prevNextData(activeSection,getSections,URLParameter)
	}

}

function tpPrevNext(getDatascrollVal){
	gsap.to(window, {
		scrollTo: {
			y: getDatascrollVal,
			autoKill: false
		},
		duration: 1
	});
}

function prevNextData(activeSection,getSections,URLParameter){
	let getDatascrollVal = getSections[activeSection].dataset.scrollOffset
		getDatascrollVal = Math.ceil(getDatascrollVal)
		tpPrevNext(getDatascrollVal)
}

function navDots(container,URLParameter){
	var dotItems = container[0].querySelectorAll('.tp-carodots-item');
	if( dotItems.length > 0 ){
		dotItems.forEach(function(self){
			self.addEventListener('click', (e) =>{
				if (self.dataset && self.dataset.scrollid) {
					let FindSection = document.querySelectorAll(`#${self.dataset.scrollid}`);
					if (FindSection.length > 0) {
						let getdata = FindSection[0].dataset.scrollOffset;
						tpScrollTo(getdata)
					}
					if (URLParameter) {
						setTimeout(() => {
							document.location.hash = self.dataset.scrollid;
						}, 100);
					}
				}
			});
			
		})
	}
}

function tpScrollTo(getdata){
	gsap.to(window, {
		scrollTo: {
			y: getdata,
			autoKill: false
		},
		duration: 1
	});
}
