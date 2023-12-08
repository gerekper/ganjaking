/*Age Gate*/
(function ($) {
	"use strict";
	var WidgetAgeGateHandler = function($scope, $) {		
		
		if(elementorFrontend.isEditMode()){
			localStorage.removeItem("max-age-expire");
		}else if(!elementorFrontend.isEditMode()){
			var container = $scope.find('.tp-agegate-wrapper'),
			age_cookies_days  = container.data('age_cookies_days'),
			exd = localStorage.getItem("max-age-expire");
			//container.closest("body").find("header").css("display","none");
			container.closest("body").css("overflow","hidden");
			var cdate = new Date();
			var endDate = new Date();
			endDate.setDate(cdate.getDate()+age_cookies_days);
			
			
			if(exd!='' && exd!=undefined && (new Date(cdate) <= new Date(exd))){				
				$('.tp-agegate-wrapper').hide();
				container.closest("body").css("overflow","");
			}else if(exd!='' && exd!=undefined && (new Date(cdate) > new Date(exd))){
				localStorage.removeItem("max-age-expire");
				$('.tp-agegate-wrapper').show();
			}else{				
				$('.tp-agegate-wrapper').show();
			} 

			/*method 1*/
			if($scope.find('.tp-agegate-wrapper.tp-method-1').length){
				
				$(".age_vmc").on("change", function(){
					if($(this).closest('.agc_checkbox').find(".age_vmc").is(":checked")){	
						$('.age_vms').find(".age_vmb").attr("disabled", false);
						$('.age_vms').find(".age_vmb").css("opacity", "1");						
						$(".age_vmb").on("click", function(){
							localStorage.setItem("max-age-expire", endDate );
							$(this).closest(".tp-agegate-wrapper").hide();
							//$(this).closest("body").find("header").css("display","block");
							$(this).closest("body").css("overflow","");
						}); 	
					}else{
						$('.age_vms').find(".age_vmb").attr("disabled", true);
						$('.age_vms').find(".age_vmb").css("opacity", "0.5");
					}
				});  
			}
			/*method 1*/
			
			/*method 2*/
			if($scope.find('.tp-agegate-wrapper.tp-method-2').length){
				
				$(".age_verify_method_btnsubmit").on("click", function(){
					var birthYear = new Date(Date.parse($(this).closest('.tp-agegate-method').find('.age_verify_birthdate').val())),					
					agebirth = birthYear.getFullYear(),
					currentYear = cdate.getFullYear(),
					userage = currentYear - agebirth,
					agelimit = $(this).closest('.tp-agegate-wrapper').data("userbirth");  
					if(userage < agelimit){  
						$(this).closest('.tp-agegate-boxes').find('.tp-age-wm').show();
					}else{	
						localStorage.setItem("max-age-expire", endDate );
						$(this).closest('.tp-agegate-wrapper').hide();
						//$(this).closest("body").find("header").css("display","block");
						$(this).closest("body").css("overflow","");
				   }
				}); 
			}
			/*method 2*/
			
			/*method 3*/
			if($scope.find('.tp-agegate-wrapper.tp-method-3').length){	
				$(".tp-agegate-wrapper .tp-age-btn-yes").on("click", function(){
					localStorage.setItem("max-age-expire", endDate );
					$(this).closest('.tp-agegate-wrapper').hide();
					//$(this).closest("body").find("header").css("display","block");
					$(this).closest("body").css("overflow","");
				});
				$(".tp-agegate-wrapper .tp-age-btn-no").on("click", function(){
					$(this).closest('.tp-agegate-boxes').find('.tp-age-wm').show();
				});
			}
			/*method 3*/
		}
	}
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-age-gate.default', WidgetAgeGateHandler);
	});
})(jQuery);