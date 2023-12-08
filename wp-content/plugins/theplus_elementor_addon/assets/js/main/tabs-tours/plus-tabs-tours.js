/*tabs & tours*/(function($) {
	"use strict";
	var WidgetTabHandler = function ($scope, $) {
		var container = $scope[0].querySelectorAll('.theplus-tabs-wrapper'),
            $currentTab = $scope.find('.theplus-tabs-wrapper'),
            $TabHover = $currentTab.data('tab-hover'),
            $Tabletmode = $currentTab.data('tab-tabletmode'),
            $Mobilemode = $currentTab.data('tab-mobilemode'),
            $ForceCloseTablet = $currentTab.data('tab-closeforce-tablet'),
            $ForceCloseMobile = $currentTab.data('tab-closeforce-mobile'),
            $TabAutoplay = $currentTab.data('tab-autoplay'),
            $TabAutoplayDuration = $currentTab.data('tab-autoplay-duration'),
            $currentTabId = '#' + $currentTab.attr('id').toString();

		let GetTabHeader = $scope[0].querySelectorAll(`${$currentTabId} ul.plus-tabs-nav li .plus-tab-header`),
			GetTabContent = $scope[0].querySelectorAll(`${$currentTabId} .theplus-tabs-content-wrapper .plus-tab-content`),
			default_active = (container[0].dataset.tabDefault) ? Number(container[0].dataset.tabDefault) : '',
			responsive_Device = document.querySelector("body").dataset.elementorDeviceMode;
			
			if($Tabletmode != undefined && $Tabletmode=='1' && responsive_Device === "tablet" || $Mobilemode != undefined && $Mobilemode=='1' && responsive_Device === "mobile"){
				default_active='0';					
			}
			
			if($ForceCloseTablet != undefined && $ForceCloseTablet=='1' && responsive_Device === "tablet" || $ForceCloseMobile != undefined && $ForceCloseMobile=='1' && responsive_Device === "mobile"){
				default_active='-1';
			}

			if($TabAutoplay !== 'yes'){
				var Connection = container[0].dataset.connection,
					row_bg_conn = (container[0].dataset.rowBgConn) ? container[0].dataset.rowBgConn : '';

					if( GetTabHeader.length > 0 ){
						GetTabHeader.forEach(function(self, idx){							
							if(default_active == idx){
								self.classList.remove('inactive');
								self.classList.add('active');

								if(Connection &&  document.querySelectorAll(`.${Connection}`).length){
									setTimeout(function(){
										plus_tabs_connection(parseInt(default_active+1),Connection);
									}, 150);
								}

								if( row_bg_conn && document.querySelectorAll(`#${row_bg_conn}`).length ){
									background_accordion_tabs_conn(default_active+1,row_bg_conn);
								}
							}else{
								self.classList.add('inactive');
							}
						});
					}
			}

			if(GetTabContent.length > 0){
				GetTabContent.forEach(function(self, idx){
					if( default_active == idx ) {
						self.classList.remove('inactive');
						self.classList.add('active');

						let FindMetro = $scope[0].querySelectorAll(`${$currentTabId} .list-isotope-metro .post-inner-loop`),
							FindGrid = $scope[0].querySelectorAll(`${$currentTabId} .list-isotope .post-inner-loop`);

							if( FindMetro.length > 0 ){
								setTimeout(function(){ 
									theplus_setup_packery_portfolio('*');	
								}, 10);
							}else if( FindGrid.length > 0 ){
								setTimeout(function(){ 
									$(FindGrid[0]).isotope('layout');
							 }, 10);
							}
					}else{
						self.classList.add('inactive');
					}
				});
			}
        
		var totalDuration = $TabAutoplayDuration * 1000,
		    totabloopplay = 0;
			if( $TabAutoplay == 'yes' && $TabAutoplayDuration != undefined){
				tp_autoplay_scroll();   

				let FindPlayPush = $scope[0].querySelectorAll(`${$currentTabId}.tp-tab-playloop.tp-tab-playpause-button`),
					PlayPushwrap = container[0].querySelectorAll(".tp-tab-play-pause-wrap");
					if( FindPlayPush.length > 0 && PlayPushwrap.length > 0){
						PlayPushwrap[0].addEventListener("click", function(e){
							let child1 = PlayPushwrap[0].querySelectorAll('.tp-tab-play-pause:nth-child(1)'),
								child2 = PlayPushwrap[0].querySelectorAll('.tp-tab-play-pause:nth-child(2)');
							
								if(child1[0].classList.contains('active')) {
									child1[0].classList.remove('active');
									child2[0].classList.add('active');
									PlayPushwrap[0].classList.add('pausecls');
									clearInterval(totabloopplay);
								}else if (child2[0].classList.contains('active')){
									child2[0].classList.remove('active');
									child1[0].classList.add('active');
									PlayPushwrap[0].classList.remove('pausecls');
									clearInterval(totabloopplay);
									tabautoplaychange();
								}
						});
					}
					
				let FindPlayheader = $scope[0].querySelectorAll(`${$currentTabId}.tp-tab-playloop ul.plus-tabs-nav li .plus-tab-header`);
					if( FindPlayheader.length > 0 ){
						FindPlayheader.forEach(function(self){
							self.addEventListener("click", function(e){
								if( FindPlayPush.length > 0 && PlayPushwrap.length > 0){
									PlayPushwrap[0].querySelector('.tp-tab-play-pause:nth-child(2)').classList.remove('active');
									PlayPushwrap[0].querySelector('.tp-tab-play-pause:nth-child(1)').classList.add('active');
								}
								clearInterval(totabloopplay);
								totabloopplay = setInterval(tabautoplaychange, totalDuration);
							});
						});
					}

				totabloopplay = setInterval(tabautoplaychange, totalDuration);
				function tabautoplaychange(){
					let tab_index,
						PlayHeader = container[0].querySelectorAll('.plus-tab-header.active'),
						PlayContent = container[0].querySelectorAll('.theplus-tabs-content-wrapper .plus-tab-content');

						if( PlayHeader.length > 0 ){
							PlayHeader.forEach(function(self){
								tab_index = self.dataset.tab;
								if( self.parentElement && self.parentElement.nextElementSibling ){
									self.classList.remove('active');
									self.classList.add('inactive');
	
									let gg = self.parentElement.nextElementSibling.querySelectorAll('.plus-tab-header')
									if(gg.length > 0){
										gg[0].classList.remove('inactive');
										gg[0].classList.add('active');
									}
								}else{
									clearInterval(totabloopplay);
									return;
								}
							});
						}

						if( PlayContent.length > 0 ){
							PlayContent.forEach(function(self){
								if( self.classList.contains('active') ){
									self.classList.remove('active');
									self.classList.add('inactive');
								}
								let gettabid12 = container[0].querySelectorAll('.plus-tab-header.active');
								if( gettabid12.length > 0 ){
									if( self.dataset.tab == gettabid12[0].dataset.tab ){
										self.classList.add('active');
										self.classList.remove('inactive');
                                        tp_resize_layout(self);
									}
								}
							});
						}

						/*carousel-remote*/ 
						let carodots = document.querySelectorAll(".tp-carousel-dots"),
							tpremote = document.querySelectorAll(".theplus-carousel-remote"),
							Connection = (container[0].dataset && container[0].dataset.connection) ? container[0].dataset.connection : '';
                            if( carodots.length > 0 && tpremote.length > 0 ){	
                                if( tpremote[0].dataset.connection == Connection ){
                                    let carodotsitem = carodots[0].querySelectorAll(".tp-carodots-item");
                                        carodotsitem.forEach(function(self){
                                            
                                            if( self.classList.contains('active') ){
                                                self.classList.remove('active');
                                                self.classList.add('inactive');
                                            }
                                            if( self.classList.contains('default-active') ){
                                                self.classList.remove('default-active');
                                                self.classList.add('inactive');
                                            }
                                        }); 

                                        carodotsitem.forEach(function(self){
                                            if(Number(tab_index) == Number(self.dataset.tab)){
                                                self.classList.remove('inactive');
                                                self.classList.add('active');
                                            }
                                        });
                                }
                            }
					
                            if(Connection && document.querySelectorAll(`.${Connection}`).length > 0){
                                plus_tabs_connection( Number(tab_index) + 1, Connection );
                            }
				}
			}

		if($TabHover == 'no'){
			$($currentTabId + ' ul.plus-tabs-nav li .plus-tab-header').on('click',function(){
				var currentTabIndex = this.dataset.tab,
					tabsContainer = this.closest('.theplus-tabs-wrapper'),
					Connection = tabsContainer.dataset.connection,
					row_bg_conn = tabsContainer.dataset.rowBgConn,
					tabsContent = tabsContainer.querySelectorAll('.theplus-tabs-content-wrapper .plus-tab-content'),
					tabHeader = tabsContainer.querySelectorAll(".theplus-tabs-nav-wrapper .plus-tab-header");

					if( this.classList.contains('active') ){
						tp_secondclick(this, currentTabIndex, tabsContent);
						return;
					}
				
					tabHeader.forEach(function(self){
						self.classList.remove('active');
						self.classList.remove('default-active');
						self.classList.add('inactive');	
					});
				
					this.classList.add('active');
					this.classList.remove('inactive');
				
					tabsContent.forEach(function(self){
						if(self.dataset.tab == currentTabIndex){
							self.classList.remove('inactive');
							self.classList.add('active');	
				
							if( self.querySelectorAll('.pt_plus_before_after').length > 0 ){
								size_Elements()
							}
						}else{
							self.classList.remove('active');
							self.classList.add('inactive');	
						}
							self.classList.remove('default-active');
					});
				
					if(Connection && document.querySelectorAll(`.${Connection}`).length > 0){
						plus_tabs_connection(currentTabIndex, Connection);
					}
				
					let carouselremote = document.querySelectorAll(".theplus-carousel-remote");
					if( carouselremote.length > 0){
						carouselremote.forEach(function(self){
							let carodots = self.querySelectorAll(".tp-carousel-dots");
								if(self.dataset.connection == Connection){
									let carodotsitem = carodots[0].querySelectorAll('.tp-carodots-item');
									if( carodotsitem.length > 0 ){
										carodotsitem.forEach(function(item){
											if( item.dataset.tab == (Number(currentTabIndex) - 1) ){
												item.classList.remove('inactive');
												item.classList.add('active');
											}else{
												item.classList.remove('active');
												item.classList.remove('default-active');
												item.classList.add('inactive');
											}
										});
									} 
								}
						});
					}
				
					if(row_bg_conn && document.querySelectorAll(`#${row_bg_conn}`).length > 0 ){
						background_accordion_tabs_conn(currentTabIndex, row_bg_conn);
					}

					if($($currentTabId+" .list-carousel-slick > .post-inner-loop").length){
						$($currentTabId+" .list-carousel-slick > .post-inner-loop").slick('setPosition');	
					}
					
				if($($currentTabId+" .elementor-background-video-embed").length){
					setTimeout(function(){
					var e = $('.theplus-tabs-content-wrapper',tabsContainer).find("[data-tab='"+currentTabIndex+"'] .elementor-background-video-embed").closest(".elementor-background-video-container").outerWidth()
					  , t = $('.theplus-tabs-content-wrapper',tabsContainer).find("[data-tab='"+currentTabIndex+"'] .elementor-background-video-embed").closest(".elementor-background-video-container").outerHeight()
					  , n = "16:9".split(":")
					  , i = n[0] / n[1]
					  , o = e / t > i;
						var w= o ? e : t * i,
						h= o ? e / i : t;
						if(h==100){
							w="100%";h="100%";
						}
						$($currentTabId+" .elementor-background-video-embed").width(w).height(h);
					}, 50);
				}

				if($($currentTabId+" iframe.pt-plus-bg-video").length){
					setTimeout(function(){
					var e = $($currentTabId+" iframe.pt-plus-bg-video").closest(".columns-video-bg").outerWidth()
					  , t = $($currentTabId+" iframe.pt-plus-bg-video").closest(".columns-video-bg").outerHeight()
					  , n = "16:9".split(":")
					  , i = n[0] / n[1]
					  , o = e / t > i;
						var w= o ? e : t * i,
						h= o ? e / i : t;
						$($currentTabId+" iframe.pt-plus-bg-video").width(w).height(h);
					}, 100);
				}

				let FindMetro = $scope[0].querySelectorAll(`${$currentTabId} .plus-tab-content[data-tab="${currentTabIndex}"] .list-isotope-metro`);
					if( FindMetro.length ){
						FindMetro.forEach(function(self){
							var container = self.querySelectorAll('.post-inner-loop'),
								uid = self.dataset.id,
								columns = self.dataset.metroColumns,
								metro_style = self.dataset.metroStyle;

								theplus_backend_packery_portfolio(uid, columns, metro_style);
								$(container[0]).isotope('layout');
						});
					}

				let FindGrid = $scope[0].querySelectorAll(`${$currentTabId} .plus-tab-content[data-tab="${currentTabIndex}"] .list-isotope .post-inner-loop`);
					if( FindGrid.length ){
						setTimeout(function(){
							$(FindGrid[0]).isotope('layout');
						}, 30);
					}

				let unfoldwrapper = $scope[0].querySelectorAll(`${$currentTabId} .plus-tab-content[data-tab="${currentTabIndex}"] .tp-unfold-wrapper`);
					if( unfoldwrapper.length ){
						unfoldwrapper.forEach(function(self){
							let unfolinner = self.querySelectorAll('.tp-unfold-description .tp-unfold-description-inner'),
								unfoldunfold = self.querySelectorAll('.tp-unfold-last-toggle'),
								unfoldescription = self.querySelectorAll('.tp-unfold-description'),
								get_height_of_div = unfolinner[0].getBoundingClientRect().height,
								data_content_max_height = self.dataset.contentMaxHeight,
								data_id = self.dataset.id;

								if( get_height_of_div <= data_content_max_height ){
									if(unfoldunfold.length > 0){
										unfoldunfold[0].style.cssText = "display: none;";
									}
									if(unfoldescription.length > 0){
										// confu
										$($currentTabId+" .plus-tab-content[data-tab='"+currentTabIndex+"'] .tp-unfold-description").append("<style>.tp-unfold-wrapper."+ data_id + " .tp-unfold-description:after{min-height:0 !important;}</style>");
									}
								}else{
									if(unfoldunfold.length > 0){
										unfoldunfold[0].style.cssText = "display: flex;";
									}
								}
						});
					}
			});
		}else if($TabHover == 'yes'){
			$($currentTabId + ' ul.plus-tabs-nav li .plus-tab-header').mouseover(function(){
				var currentTabIndex = this.dataset.tab,
					tabsContainer = this.closest('.theplus-tabs-wrapper'),
					Connection = tabsContainer.dataset.connection,
					row_bg_conn = tabsContainer.dataset.rowBgConn,
					tabContent = tabsContainer.querySelectorAll('.theplus-tabs-content-wrapper .plus-tab-content'),
					tabsheader1 = this.closest('.theplus-tabs-wrapper > .theplus-tabs-nav-wrapper'),
                    tabHeader = tabsheader1.querySelectorAll('.plus-tab-header');
				
					if( this.classList.contains('active') ){
						tp_secondclick(this, currentTabIndex, tabContent);
						return;
					}

					tabHeader.forEach(function(self){
						self.classList.remove('active');
						self.classList.remove('default-active');
						self.classList.add('inactive');	
					});

					this.classList.add('active');
					this.classList.remove('inactive');

					// tabContent.forEach(function(self){
					// 	if(self.dataset.tab == currentTabIndex){
					// 		self.classList.remove('inactive');
					// 		self.classList.add('active');	

					// 		if( self.querySelectorAll('.pt_plus_before_after').length > 0 ){
					// 			size_Elements()
					// 		}
					// 	}else{
					// 		self.classList.remove('active');
					// 		self.classList.add('inactive');	
					// 	}
					// 		self.classList.remove('default-active');
					// });
                    
                    $(tabsContainer).find(">.theplus-tabs-content-wrapper>.plus-tab-content").removeClass('active').addClass('inactive');
				
				    $(">.theplus-tabs-content-wrapper>.plus-tab-content[data-tab='"+currentTabIndex+"']",tabsContainer).addClass('active').removeClass('inactive');

					if(Connection && document.querySelectorAll(`.${Connection}`).length > 0){
						plus_tabs_connection(currentTabIndex, Connection);
					}
				
					let carouselremote = document.querySelectorAll(".theplus-carousel-remote");
					if( carouselremote.length > 0){
						carouselremote.forEach(function(self){
							let carodots = self.querySelectorAll(".tp-carousel-dots");
								if(self.dataset.connection == Connection){
									let carodotsitem = carodots[0].querySelectorAll('.tp-carodots-item');
									if( carodotsitem.length > 0 ){
										carodotsitem.forEach(function(item){
											if( item.dataset.tab == (Number(currentTabIndex) - 1) ){
												item.classList.remove('inactive');
												item.classList.add('active');
											}else{
												item.classList.remove('active');
												item.classList.remove('default-active');
												item.classList.add('inactive');
											}
										});
									} 
								}
						});
					}

					if(row_bg_conn && document.querySelectorAll(`#${row_bg_conn}`).length > 0  ){
						background_accordion_tabs_conn(currentTabIndex,row_bg_conn);
					}

					if($($currentTabId+" .list-carousel-slick > .post-inner-loop").length){
						$($currentTabId+" .list-carousel-slick > .post-inner-loop").slick('setPosition');
					}

					if($($currentTabId+" .elementor-background-video-embed").length){
						setTimeout(function(){
						var e = $('.theplus-tabs-content-wrapper',tabsContainer).find("[data-tab='"+currentTabIndex+"'] .elementor-background-video-embed").closest(".elementor-background-video-container").outerWidth()
							, t = $('.theplus-tabs-content-wrapper',tabsContainer).find("[data-tab='"+currentTabIndex+"'] .elementor-background-video-embed").closest(".elementor-background-video-container").outerHeight()
							, n = "16:9".split(":")
							, i = n[0] / n[1]
							, o = e / t > i;
								var w= o ? e : t * i,
								h= o ? e / i : t;
								if(h==100){
									w="100%";h="100%";
								}
								$($currentTabId+" .elementor-background-video-embed").width(w).height(h);
						}, 50);
					}

					if($($currentTabId+" iframe.pt-plus-bg-video").length){
						setTimeout(function(){
							var e = $($currentTabId+" iframe.pt-plus-bg-video").closest(".columns-video-bg").outerWidth()
								, t = $($currentTabId+" iframe.pt-plus-bg-video").closest(".columns-video-bg").outerHeight()
								, n = "16:9".split(":")
								, i = n[0] / n[1]
								, o = e / t > i;
									var w= o ? e : t * i,
									h= o ? e / i : t;
									$($currentTabId+" iframe.pt-plus-bg-video").width(w).height(h);
						}, 100);
					}

					let FindMetro = $scope[0].querySelectorAll(`${$currentTabId} .plus-tab-content[data-tab="${currentTabIndex}"] .list-isotope-metro`);
					if( FindMetro.length){
						FindMetro.forEach(function(self){
							var container = self.querySelectorAll('.post-inner-loop'),
								uid = self.dataset.id,
								columns = self.dataset.metroColumns,
								metro_style = self.dataset.metroStyle;

								theplus_backend_packery_portfolio(uid, columns, metro_style);
								$(container[0]).isotope('layout');
						});
					}

					let FindGrid = $scope[0].querySelectorAll(`${$currentTabId} .plus-tab-content[data-tab="${currentTabIndex}"] .list-isotope .post-inner-loop`);
					if( FindGrid.length ){
						setTimeout(function(){
							$(FindGrid[0]).isotope('layout');
						}, 30);
					}

					let unfoldwrapper = $scope[0].querySelectorAll(`${$currentTabId} .plus-tab-content[data-tab="${currentTabIndex}"] .tp-unfold-wrapper`);
					if( unfoldwrapper.length ){
						unfoldwrapper.forEach(function(self){
							let unfolinner = self.querySelectorAll('.tp-unfold-description .tp-unfold-description-inner'),
								unfoldunfold = self.querySelectorAll('.tp-unfold-last-toggle'),
								unfoldescription = self.querySelectorAll('.tp-unfold-description'),
								get_height_of_div = unfolinner[0].getBoundingClientRect().height,
								data_content_max_height = self.dataset.contentMaxHeight,
								data_id = self.dataset.id;

								if( get_height_of_div <= data_content_max_height ){
									if(unfoldunfold.length > 0){
										unfoldunfold[0].style.cssText = "display: none;";
									}
									if(unfoldescription.length > 0){
										// confu
										$($currentTabId+" .plus-tab-content[data-tab='"+currentTabIndex+"'] .tp-unfold-description").append("<style>.tp-unfold-wrapper."+ data_id + " .tp-unfold-description:after{min-height:0 !important;}</style>");
									}
								}else{
									if(unfoldunfold.length > 0){
										unfoldunfold[0].style.cssText = "display: flex;";
									}
								}
						});
					}
			});
		}
		
		/*
		 *	Swiper Tabbing
		*/
		var swiper_loop = false;
		if( container[0].classList.contains('swiper-container') ){
			let $TabSwiperLoop = container[0].dataset.swiperLoop;

			if($TabSwiperLoop === 'yes'){
				swiper_loop = true;
			}

			new Swiper(".theplus-tabs-wrapper.swiper-container",{
				slidesPerView: "auto",
				mousewheelControl: !0,
				freeMode: !0,
				loop : swiper_loop,
                observer: true,
                observeParents: true,
			});
		}
		
		var swiper_loop_slide = false,
			swiper_centermode = false,
			swipercenteredSlidesBounds = false,
            $sppagev = "auto";
		if( container[0].querySelectorAll('.theplus-tabs-nav-wrapper.swiper-container').length > 0 ){
			var $TabSwiperLoopSlide = container[0].dataset.swiperLoop,
				$TabSwiperCenter = container[0].dataset.swiperCentermode,			
				$TabSwiperCenterSlidePerPage = container[0].dataset.swiperCentermodeslideperview;			
				
				if($TabSwiperLoopSlide === 'yes'){
					swiper_loop_slide = true;
					swipercenteredSlidesBounds = true;
				}
                
				if($TabSwiperCenter === 'yes'){
					swiper_centermode = true;
                    //$sppagev = $TabSwiperCenterSlidePerPage;
				}
               
			var mySwiper = new Swiper(".theplus-tabs-wrapper .theplus-tabs-nav-wrapper.swiper-container",{
				navigation: {
					nextEl: '.tp-swiper-button-next',
					prevEl: '.tp-swiper-button-prev'
				},
				slidesPerView: $sppagev,
				updateOnWindowResize: true,
				loop : swiper_loop_slide,
				centeredSlides: swiper_centermode,
				centeredSlidesBounds: swipercenteredSlidesBounds,
                slideToClickedSlide: true,
                observer: true,
                observeParents: true,
				on: {
					click(event) {
                        tp_swipeclick(this);
                        
                        // if($TabSwiperCenter === 'yes'){
                        //     mySwiper.slideTo(this.clickedIndex);                            
                        // }
					},
				},
			});
		}

		if( container[0].classList.contains('mobile-accordion') ){
			window.addEventListener("resize", function(){
				if(window.innerWidth <= 600){
					container[0].classList.add('mobile-accordion-tab');
				}
			});

			let GetMobileTitle = container[0].querySelectorAll('.theplus-tabs-content-wrapper .elementor-tab-mobile-title')
				if( GetMobileTitle.length > 0 ){
					GetMobileTitle.forEach(function(self){
                        var currentTabIndex = self.dataset.tab,
                            tabsContainer = self.closest('.theplus-tabs-wrapper'),
                            Connection = tabsContainer.dataset.connection,
                            row_bg_conn = tabsContainer.dataset.rowBgConn,
                            tabsContent = tabsContainer.querySelectorAll('.theplus-tabs-content-wrapper .plus-tab-content'),
                            tabHeader = tabsContainer.querySelectorAll(".theplus-tabs-nav-wrapper .plus-tab-header"),
                            tabTitle = tabsContainer.querySelectorAll(".theplus-tabs-content-wrapper .elementor-tab-mobile-title");

							if( tabTitle.length > 0 ){
								tabTitle[0].classList.add('active');
							}
                            
							self.addEventListener("click", function(){

                                if( this.classList.contains('active') ){
                                    tp_secondclick(this, currentTabIndex, tabsContent);
                                    return;
                                }

								if( tabTitle.length > 0 ){
									tabTitle.forEach(function(self){
										self.classList.remove('active');
										self.classList.remove('default-active');
										self.classList.add('inactive');	
									});
								}

								this.classList.add('active');
								this.classList.remove('inactive');
								
								if( tabsContent.length > 0 ){
									tabsContent.forEach(function(self){
										if(self.dataset.tab == currentTabIndex){
											self.classList.remove('inactive');
											self.classList.add('active');	
				
											if( self.querySelectorAll('.pt_plus_before_after').length > 0 ){
												size_Elements()
											}
										}else{
											self.classList.remove('active');
											self.classList.add('inactive');	
										}
											self.classList.remove('default-active');
									});
								}

								if(Connection && document.querySelectorAll(`.${Connection}`).length > 0){
									plus_tabs_connection(currentTabIndex, Connection);
								}

								if(row_bg_conn && document.querySelectorAll(`#${row_bg_conn}`).length > 0  ){
									background_accordion_tabs_conn(currentTabIndex,row_bg_conn);
								}

								if($($currentTabId+" .list-carousel-slick > .post-inner-loop").length){
									$($currentTabId+" .list-carousel-slick > .post-inner-loop").slick('setPosition');
								}
								if($($currentTabId+" .elementor-background-video-embed").length){
									setTimeout(function(){
									var e = $('.theplus-tabs-content-wrapper',tabsContainer).find("[data-tab='"+currentTabIndex+"'] .elementor-background-video-embed").closest(".elementor-background-video-container").outerWidth()
									, t = $('.theplus-tabs-content-wrapper',tabsContainer).find("[data-tab='"+currentTabIndex+"'] .elementor-background-video-embed").closest(".elementor-background-video-container").outerHeight()
									, n = "16:9".split(":")
									, i = n[0] / n[1]
									, o = e / t > i;
										var w= o ? e : t * i,
										h= o ? e / i : t;
										if(h==100){
											w="100%";h="100%";
										}
										$($currentTabId+" .elementor-background-video-embed").width(w).height(h);
									}, 50);
								}
								if($($currentTabId+" iframe.pt-plus-bg-video").length){
									setTimeout(function(){
									var e = $($currentTabId+" iframe.pt-plus-bg-video").closest(".columns-video-bg").outerWidth()
									, t = $($currentTabId+" iframe.pt-plus-bg-video").closest(".columns-video-bg").outerHeight()
									, n = "16:9".split(":")
									, i = n[0] / n[1]
									, o = e / t > i;
										var w= o ? e : t * i,
										h= o ? e / i : t;
										$($currentTabId+" iframe.pt-plus-bg-video").width(w).height(h);
									}, 100);
								}

							let FindMetro = $scope[0].querySelectorAll(`${$currentTabId} .plus-tab-content[data-tab="${currentTabIndex}"] .list-isotope-metro`);
								if( FindMetro.length){
									FindMetro.forEach(function(self){
										var container = self.querySelectorAll('.post-inner-loop'),
											uid = self.dataset.id,
											columns = self.dataset.metroColumns,
											metro_style = self.dataset.metroStyle;

											theplus_backend_packery_portfolio(uid, columns, metro_style);
											$(container[0]).isotope('layout');
									});
								}

							let FindGrid = $scope[0].querySelectorAll(`${$currentTabId} .plus-tab-content[data-tab="${currentTabIndex}"] .list-isotope .post-inner-loop`);
								if( FindGrid.length ){
									setTimeout(function(){
										$(FindGrid[0]).isotope('layout');
									}, 30);
								}

							let unfoldwrapper = $scope[0].querySelectorAll(`${$currentTabId} .plus-tab-content[data-tab="${currentTabIndex}"] .tp-unfold-wrapper`);
								if( unfoldwrapper.length ){
									unfoldwrapper.forEach(function(self){
										let unfolinner = self.querySelectorAll('.tp-unfold-description .tp-unfold-description-inner'),
											unfoldunfold = self.querySelectorAll('.tp-unfold-last-toggle'),
											unfoldescription = self.querySelectorAll('.tp-unfold-description'),
											get_height_of_div = unfolinner[0].getBoundingClientRect().height,
											data_content_max_height = self.dataset.contentMaxHeight,
											data_id = self.dataset.id;

											if( get_height_of_div <= data_content_max_height ){
												if(unfoldunfold.length > 0){
													unfoldunfold[0].style.cssText = "display: none;";
												}
												if(unfoldescription.length > 0){
													// confu
													$($currentTabId+" .plus-tab-content[data-tab='"+currentTabIndex+"'] .tp-unfold-description").append("<style>.tp-unfold-wrapper."+ data_id + " .tp-unfold-description:after{min-height:0 !important;}</style>");
												}
											}else{
												if(unfoldunfold.length > 0){
													unfoldunfold[0].style.cssText = "display: flex;";
												}
											}
									});
								}
						});
					});	
				}
		}	

		let hash = window.location.hash,
			$FindID = container[0].querySelectorAll(`${hash}.plus-tab-header`);
			if( hash && $FindID.length > 0 ){
				$FindID.forEach(function(self){
					if( !self.classList.contains('active') ){
						document.querySelector('html, body').animate({
							scrollTop: $(hash).offset().top,
						}, 1500);
						self.click();

						if( container[0].querySelectorAll(`.elementor-tab-mobile-title[data-tab="${self.dataset.tab}"`).length > 0 ){
							container[0].querySelector(`.elementor-tab-mobile-title[data-tab="${self.dataset.tab}"`).click()
						}
					}
				});
			}
        
        function tp_swipeclick(event){
            let Getslideindex = (event.clickedSlide) ? event.clickedSlide.dataset.swiperSlideIndex : 0,
                Totalslides = (event.slides) ? Object.values(event.slides) : [],
                tabContent = container[0].querySelectorAll('.theplus-tabs-content-wrapper .plus-tab-content');
                
                Totalslides.forEach(function(self){
                    if( self && self.dataset && self.dataset.swiperSlideIndex && Number(self.dataset.swiperSlideIndex) == Number(Getslideindex) ){
                        self.firstChild.classList.remove('inactive');
						self.firstChild.classList.add('active');	
                    }else{
                        if(self && self.firstChild){
                            self.firstChild.classList.remove('active');
						    self.firstChild.classList.add('inactive');	
                        }
                    }
                    
                });

				if( tabContent.length > 0 ){                    
					tabContent.forEach(function(self){
						if( Number(self.dataset.tab) == Number(Getslideindex) + 1 ){
							self.classList.remove('inactive');
							self.classList.add('active');
						}else{
							self.classList.remove('active');
							self.classList.remove('default-active');
							self.classList.add('inactive');
						}
					});
				}
        }

		function tp_secondclick($this, currentTabIndex, tabContent){
				if( container[0].dataset.tabSecond ){
					$this.classList.remove('active');
					$this.classList.add('inactive');	

					if( tabContent.length > 0 ){
						tabContent.forEach(function(self){
							if( currentTabIndex == self.dataset.tab ){
								self.classList.remove('active');
								self.classList.add('inactive');	
							}
						});
					}
				}
		}

        function tp_autoplay_scroll(){
            var windowHeightt = window.innerHeight,
                elementTopp = container[0].getBoundingClientRect().top,
                elementVisiblee = 150;

                if (elementTopp < windowHeightt - elementVisiblee) {
                    tp_scroll_play();
                }else{
                    window.addEventListener("scroll", tp_scroll_play);
                }
        }

        function tp_scroll_play(){
            let windowHeight = window.innerHeight,
                elementTop = document.querySelector(".theplus-tabs-wrapper").getBoundingClientRect().top,
                elementVisible = 150;

                if (elementTop < windowHeight - elementVisible) {
                    $scope[0].querySelector(`${$currentTabId} ul.plus-tabs-nav li .plus-tab-header`).classList.remove('inactive');
                    $scope[0].querySelector(`${$currentTabId} ul.plus-tabs-nav li .plus-tab-header`).classList.add('active');
					window.removeEventListener("scroll", tp_scroll_play);
                }
				
        }

        function tp_resize_layout(self){
            let FindGrid = self.querySelectorAll(`.list-isotope .post-inner-loop`),
                carouselslick = self.querySelectorAll(`.list-carousel-slick .post-inner-loop`),
                Metrolayout = self.querySelectorAll(`.list-isotope-metro .post-inner-loop`);

                if( FindGrid.length ){
                    setTimeout(function(){
                        $(FindGrid[0]).isotope('layout');
                    }, 30);
                }

                if( carouselslick.length ){
                    setTimeout(function(){
                        $(carouselslick[0]).slick('setPosition');	
                    }, 30);
                }

                if( Metrolayout.length ){
                    setTimeout(function(){
                        Metrolayout.forEach(function(item){
                            if(item.parentElement && item.parentElement.dataset){
                                let uid = (item.parentElement.dataset.id) ? item.parentElement.dataset.id : '',
                                    columns = (item.parentElement.dataset.metroColumns) ? item.parentElement.dataset.metroColumns : '',
                                    metro_style = (item.parentElement.dataset.metroStyle) ? item.parentElement.dataset.metroStyle : '';
    
                                    theplus_backend_packery_portfolio(uid, columns, metro_style);
                            }
                        });
                    }, 30);
                }
        }

		function Resizelayout() {
            let MetroResize = container[0].querySelectorAll('.list-isotope-metro');
            if( MetroResize.length > 0 ){
                theplus_setup_packery_portfolio("*");	
            }
        }
        Resizelayout();
	};

    window.addEventListener('elementor/frontend/init', (event) => {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-tabs-tours.default', WidgetTabHandler);
    });

})(jQuery);

function plus_tabs_connection(tab_index,connection){
	var $=jQuery;
	if(connection && $("."+connection).length==1){
		var current=$('.'+connection+' > .post-inner-loop').slick('slickCurrentSlide');
		if(current!=(tab_index-1)){
			$('.'+connection+' > .post-inner-loop').slick('slickGoTo', tab_index-1);
		}
	}
}