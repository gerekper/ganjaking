(function ($) {
	"use strict";
	var WidgetCarouselRemoteHandler = function ($scope, $) {
		var $target = $('.theplus-carousel-remote', $scope),
			dotdiv = $target.find('.tp-carousel-dots .tp-carodots-item'),
			remote_uid = $target.data("id"),
			acttab = $('.' + remote_uid + '.tp-tabs-wrapper').find(' li .tp-tab-header.active'),
			activetab = acttab.data('tab');

		var container = $scope[0].querySelectorAll('.theplus-carousel-remote'),
			rsetting = (container[0] && container[0].dataset && container[0].dataset.remotedata) ? JSON.parse(container[0].dataset.remotedata) : [],
			paginate = (rsetting && rsetting.paginate) ? 1 : 0,
			rType = (container[0] && container[0].dataset && container[0].dataset.remote) ? container[0].dataset.remote : '';

		var BackendOnly = false;
		if( elementorFrontend.isEditMode() ){
			BackendOnly = true;
		}

		if ( 'horizontal' === rType) {
			var UID = (rsetting && rsetting.u_id) ? rsetting.u_id : '',
				WidgetID = (rsetting && rsetting.widgetid) ? rsetting.widgetid : '',
				checkHR = document.querySelectorAll(`#tphs_${UID}`)

                if( checkHR.length > 0 ){
                    var GetRemote = document.querySelectorAll(`.elementor-element-${WidgetID}`),
                        hsWrap = document.querySelector(`#tphs_${UID}`);

                        carousalRemoteAdd(GetRemote,checkHR,BackendOnly,UID,WidgetID,paginate,hsWrap,rsetting)

                        if( elementorFrontend.isEditMode() ){
                            navDots(checkHR,URLParameter)
                            let horizontalData = checkHR[0].dataset.result ? JSON.parse(checkHR[0].dataset.result) : '';

                            var getSections = checkHR[0].querySelectorAll(`.tp-horizontal-scroll-wrapper .elementor > .e-con, .tp-horizontal-scroll-wrapper .elementor > .elementor-section , .tp-horizontal-scroll-wrapper > .elementor-inner > .elementor-section-wrap > .elementor-section, .tp-horizontal-scroll-wrapper elementor > .elementor-inner > .elementor-section-wrap > .e-con`),
                                activeSection = checkHR[0].dataset.activeSlide,
                                URLParameter = horizontalData && horizontalData.URLParameter ? 1 : 0;

                                jQuery(`#tphs_${UID} .nav-next-slide`).click(function(){
                                    goToNextSection(event, Number(activeSection), getSections, URLParameter);
                                });

                                jQuery(`#tphs_${UID} .nav-prev-slide`).click(function(){
                                    goToPrevSection(event, activeSection, getSections, URLParameter);
                                });
                        }
                }
		}else{
			if( $target.length ){
				$(".theplus-carousel-remote .custom-nav-remote").on("click", function (e) {
                    e.stopPropagation();
                    e.preventDefault();
	
					var remote_uid = $(this).data("id");
					var remote_type = $(this).closest(".theplus-carousel-remote").data("remote");
	
					if (remote_uid != '' && remote_uid != undefined && remote_type == 'carousel') {
	
						var carousel_slide = $(this).data("nav");
	
						if (carousel_slide == 'next') {
							$('.' + remote_uid + ' > .post-inner-loop').slick("slickNext");
						} else if (carousel_slide == 'prev') {
							$('.' + remote_uid + ' > .post-inner-loop').slick("slickPrev");
						}
	
					} else if (remote_uid != '' && remote_uid != undefined && remote_type == 'switcher') {
	
						var switcher_toggle = $(this).data("nav");
	
						var switch_toggle = $('#' + remote_uid).find('.switch-toggle');
						var switch_1_toggle = $('#' + remote_uid).find('.switch-1');
						var switch_2_toggle = $('#' + remote_uid).find('.switch-2');
	
						$(".theplus-carousel-remote .custom-nav-remote").removeClass("active");
						$(this).addClass("active");
	
						if (switcher_toggle == 'next') {
							switch_2_toggle.trigger("click");
						} else if (switcher_toggle == 'prev') {
							switch_1_toggle.trigger("click");
						}
					}
				});
	
				if (dotdiv.length > 0) {
					dotdiv.on('click', function () {
						$(this).closest(".tp-carousel-dots").find(".tp-carodots-item").removeClass('active default-active').addClass('inactive');
						$(this).removeClass('inactive').addClass('active');
						var Connection = $(this).closest(".theplus-carousel-remote").data('connection'),
							tab_index = $(this).data("tab"),
							extrId = $(this).closest(".theplus-carousel-remote").data("extra-conn");
						if (Connection != '' && Connection != undefined && $("#" + Connection).length) {
							tp_dot_connection(tab_index, Connection);
						}
						if (extrId != '' && extrId != undefined && $("." + extrId).length) {
							tp_dotex_connection(tab_index, extrId);
						}
						if ($(".carousel-pagination").length) {
							var ctab = tab_index + 1;
							$(this).closest(".theplus-carousel-remote").find(".carousel-pagination ul.pagination-list li.pagination-list-in.active").html('0' + ctab);
						}
					});
				}
			}
			
			if( elementorFrontend.isEditMode() ){
				let hh = document.querySelectorAll(`.elementor-element-${rsetting.widgetid}`);
				setTimeout(() => {
					if( BackendOnly ){
						hh.forEach(function(self){
							if(self.style.display == "none"){
								self.style.display = "block";
							}
							if( self.closest('.tp-horizontal-scroll-wrapper') ){
								self.remove()
							}
						});
					}
				}, 100);
			}
		}
	};

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-carousel-remote.default', WidgetCarouselRemoteHandler);
	});
})(jQuery);

function tp_dot_connection(tab_index, Connection) {
	var $ = jQuery;

	if ( Connection != '' && $("#" + Connection).length == 1 ) {
		var current = $('#' + Connection + ' > .post-inner-loop,#' + Connection + '.post-inner-loop').slick('slickCurrentSlide');
		if (current != (tab_index)) {
			$('#' + Connection + ' > .post-inner-loop,#' + Connection + '.post-inner-loop').slick('slickGoTo', tab_index);
		}
	}
}

function tp_dotex_connection(tab_index, id) {
	var $ = jQuery;

	if ( id != '' && $("." + id + '.tp-tabs-wrapper').length == 1 ) {
		if ( !$("." + id).find('li .tp-tab-header[data-tab="' + parseInt(tab_index + 1) + '"]').hasClass("active") ) {
			$("." + id).find('li .tp-tab-header[data-tab="' + parseInt(tab_index + 1) + '"]').trigger("click");
		}
	}
}

/** Horizontal Scroll Connection */
function carousalRemoteAdd(GetRemote, checkHR, BackendOnly, UID, WidgetID, paginate, hsWrap, rsetting) {
	var fixBg = hsWrap.querySelectorAll(`.tp-fixbg`);

	if( GetRemote.length > 0 ){
		GetRemote.forEach(function(self){
			let hsChild = document.querySelectorAll(`#tphs_${UID} .elementor`);
				if( hsChild.length > 0 ){
					hsChild[0].insertAdjacentHTML("afterbegin", self.outerHTML);
				}

				jQuery(`.elementor-element-${WidgetID}`, checkHR).addClass('cr-horizontal-scroll');
				jQuery(`.cr-horizontal-scroll`).css("display", "block");

				if( BackendOnly ){
					self.style.display = "none";
				}else{
					self.remove();
				}
		})
	}

	setTimeout(() => {
		if( paginate ){
			var hsNav = document.querySelectorAll(`#tphs_${UID} > .pin-spacer > .elementor > .elementor-section, #tphs_${UID} > .pin-spacer > .elementor > .e-con, #tphs_${UID} > .pin-spacer > .elementor > .elementor-inner > .elementor-section-wrap > .elementor-section, #tphs_${UID} > .pin-spacer > .elementor > .elementor-inner > .elementor-section-wrap > .e-con`),
                responsive_Device = document.querySelector('body').dataset.elementorDeviceMode;

                if( hsNav.length > 0 ){
                    hsNav.forEach(function(self, idx){
                        if( "desktop" === responsive_Device ){
                            if( self.classList.contains('elementor-hidden-desktop') ){
                                self.remove();
                            }
                        }else if( "tablet" === responsive_Device ){
                            if( self.classList.contains('elementor-hidden-tablet') ){
                                self.remove();
                            }
                        }else if( "mobile" === responsive_Device ){
                            if( self.classList.contains('elementor-hidden-mobile') ){
                                self.remove();
                            }
                        }
                    })
                }
	
			var paginationStyle = (rsetting && rsetting.pagination_style) ? rsetting.pagination_style : '',
				paginateSeparator = (rsetting && rsetting.paginateSeparator) ? rsetting.paginateSeparator : '',
				separatorIcon = (rsetting && rsetting.separatorIcon) ? rsetting.separatorIcon : '',
				paginationDiv = checkHR[0].querySelector('.tp-hscroll-pagination'),
				count = hsNav.length - fixBg.length,
				spVal = '';
                
				if( 'default' === paginateSeparator ){
					spVal = '/';
				}else if( 'custom' === paginateSeparator ){
					spVal = `<i class="${separatorIcon.value}"></i>`;
				}
                
				paginationDiv ? paginationDiv.innerHTML += `<div class="hscroll-pagination-slides"></div> <div class="hscroll-pagination-slides hs_separator">${spVal}</div> <div class="hscroll-pagination-slides hs_total_slides">${count}</div>` : '';

			let tpRemoveslide = checkHR[0].querySelectorAll('.hs_total_slides, .hs_separator');
				tpRemoveslide.forEach(function(self, index){
					if( index > 1 ){
						self.remove()
					}
				});

			var slidesDiv = paginationDiv.querySelector('.hscroll-pagination-slides');
				hsNav.forEach(function(self, idx){
					if(idx < count){
						slidesDiv.innerHTML += `<div class="hscroll-pagination-slides hs-current-slides ${paginationStyle}">${idx + 1}</div>`;
					}
				});
	
			var pn_firstslide = slidesDiv.querySelectorAll('.hs-current-slides:nth-child(1)'),
				pn_lastslide = slidesDiv.querySelectorAll('.hs-current-slides:nth-last-child(1)');
	
                setTimeout(() => {
                    if( !hsWrap.classList.contains('start') && !hsWrap.classList.contains('end') && pn_firstslide.length > 0){
                        pn_firstslide[0].classList.add('active');
                    }else if( hsWrap.classList.contains('end') && pn_lastslide.length > 0){
                        pn_lastslide[0].classList.add('active');
                    }
                }, 100);
		}
	}, 1200);
	
}