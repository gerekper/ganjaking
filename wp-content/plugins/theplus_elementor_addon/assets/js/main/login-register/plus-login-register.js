/*login register*/
(function ($) {
	"use strict";
	var WidgetUnfoldHandler = function($scope, $) {
		var container = $scope.find('.tp-user-register .tp-lr-f-user-pass .tp-reg-pass-hint'),
		ps = $scope.find('.tp-lr-f-user-pass .tp-password-field-show'),
		psh = $scope.find('.tp-lr-f-user-pass .tp-password-field-showh'),
		pat1 = $scope.find('.tp-pass-indicator.pattern-1'),
		pat2 = $scope.find('.tp-pass-indicator.pattern-2'),
		pat3 = $scope.find('.tp-pass-indicator.pattern-3'),
		pat4 = $scope.find('.tp-pass-indicator.pattern-4'),
		pat5 = $scope.find('.tp-pass-indicator.pattern-5'),
		passond = $scope.find('.tp-pass-indicator.pshd'),
		passonf = $scope.find('.tp-pass-indicator.pshf'),
		passonc = $scope.find('.tp-pass-indicator.pshc');
		
		if(ps.length){
			$(ps).on("click",function() {
				if($(this).hasClass("tpsi")){
					var passhideicon = $(this).data("passhideicon");
					$(this).removeClass("tpsi");
					$(this).html(passhideicon);
				}else{
					var passshowicon = $(this).data("passshowicon");
					$(this).addClass("tpsi");
					$(this).html(passshowicon);
				}
				var input = $($(this).attr("toggle"));
				if (input.attr("type") == "password") {
					input.attr("type", "text");
				} else {
					input.attr("type", "password");
				}
			});
		}
		if(psh.length){
			$(psh).on("click",function() {
				if($(this).hasClass("tpsi")){					
					$(this).removeClass("tpsi");
					$(this).closest(".tp-user-register").find(".tp-pass-indicator").fadeIn(400);
				}else{
					$(this).addClass("tpsi");
					$(this).closest(".tp-user-register").find(".tp-pass-indicator").css("display", "none");
				}
				
			});
		}
		if(container.length){
			if(passonf.length){
				$(container).on("focus keyup", function () {
					$(this).closest(".tp-user-register").find(".tp-pass-indicator").fadeIn(400);
				});
				$(container).focusout(function(){
					$(this).closest(".tp-user-register").find(".tp-pass-indicator").css("display", "none");
				});
			}
				
			
			$(container).on("focus keyup", function () {			
				var password = $(this).val(),
				cfindi = $(this).closest(".tp-user-register").find(".tp-pass-indicator"),
				cfclicki = $(this).closest(".tp-user-register").find(".tp-password-field-showh");
				
				var strength = 0;
				
				if(pat1.length || pat4.length || pat5.length){
					//min 8 character
					if (password.length > 7) {
						cfindi.find(".tp-min-eight-character").addClass("tp-pass-success-ind");
						cfindi.find(".tp-min-eight-character i").removeClass("fa-question-circle").addClass("fa-check-circle");						
						strength++;
					} else {
						cfindi.find(".tp-min-eight-character").removeClass("tp-pass-success-ind");
						cfindi.find(".tp-min-eight-character i").addClass("fa-question-circle").removeClass("fa-check-circle");
					}
				}
				
				if(pat1.length || pat2.length || pat3.length){
					//numbers
					if (password.match(/([0-9])/)) {
						cfindi.find(".tp-one-number").addClass("tp-pass-success-ind");
						cfindi.find(".tp-one-number i").removeClass("fa-question-circle").addClass("fa-check-circle");						
						strength++;
					} else {
						cfindi.find(".tp-one-number").removeClass("tp-pass-success-ind");
						cfindi.find(".tp-one-number i").addClass("fa-question-circle").removeClass("fa-check-circle");						
					}
				}
				
				if(pat1.length || pat3.length){
					//characters
					if (password.match(/([a-zA-Z])/)) {
						cfindi.find(".tp-low-lat-case").addClass("tp-pass-success-ind");
						cfindi.find(".tp-low-lat-case i").removeClass("fa-question-circle").addClass("fa-check-circle");
						strength++;
					} else {
						cfindi.find(".tp-low-lat-case").removeClass("tp-pass-success-ind");
						cfindi.find(".tp-low-lat-case i").addClass("fa-question-circle").removeClass("fa-check-circle");
					}
				}
				
				if(pat1.length){
					//special character
					if (password.match(/([!,@,#,$,%,^,&,*,?,_,~,-,(,)])/)) {
						cfindi.find(".tp-one-special-char").addClass("tp-pass-success-ind");
						cfindi.find(".tp-one-special-char i").removeClass("fa-question-circle").addClass("fa-check-circle");
						strength++;
					} else {
						cfindi.find(".tp-one-special-char").removeClass("tp-pass-success-ind");
						cfindi.find(".tp-one-special-char i").addClass("fa-question-circle").removeClass("fa-check-circle");
					}
				}
				
				if(pat2.length){
					//min 4 and max 8 character
					if (password.length > 3 && password.length < 9) {
						cfindi.find(".tp-four-eight-character").addClass("tp-pass-success-ind");
						cfindi.find(".tp-four-eight-character i").removeClass("fa-question-circle").addClass("fa-check-circle");
						strength++;
					} else {
						cfindi.find(".tp-four-eight-character").removeClass("tp-pass-success-ind");
						cfindi.find(".tp-four-eight-character i").addClass("fa-question-circle").removeClass("fa-check-circle");
					}
				}
				
				if(pat3.length){
					//min 6 character
					if (password.length > 5) {
						cfindi.find(".tp-min-six-character").addClass("tp-pass-success-ind");
						cfindi.find(".tp-min-six-character i").removeClass("fa-question-circle").addClass("fa-check-circle");
						strength++;
					} else {
						cfindi.find(".tp-min-six-character").removeClass("tp-pass-success-ind");
						cfindi.find(".tp-min-six-character i").addClass("fa-question-circle").removeClass("fa-check-circle");
					}
				}
				
				if(pat4.length || pat5.length){
					//lower and uppercase				
					if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {				
						cfindi.find(".tp-low-upper-case").addClass("tp-pass-success-ind");
						cfindi.find(".tp-low-upper-case i").removeClass("fa-question-circle").addClass("fa-check-circle");
						strength++;
					} else {
						cfindi.find(".tp-low-upper-case").removeClass("tp-pass-success-ind");
						cfindi.find(".tp-low-upper-case i").addClass("fa-question-circle").removeClass("fa-check-circle");
					}
				}				
				
				if(pat4.length){
				//numbers and characters
					if (password.match(/([a-zA-Z])/) || password.match(/([0-9])/)) {
						cfindi.find(".tp-digit-alpha").addClass("tp-pass-success-ind");
						cfindi.find(".tp-digit-alpha i").removeClass("fa-question-circle").addClass("fa-check-circle");
						strength++;
					} else {
						cfindi.find(".tp-digit-alpha").removeClass("tp-pass-success-ind");
						cfindi.find(".tp-digit-alpha i").addClass("fa-question-circle").removeClass("fa-check-circle");
					}
				}
				
				if(pat5.length){
					//special character
					if (password.match(/([!,@,#,$,%,^,&,*,?,_,~,-,(,)])/) || password.match(/([0-9])/)) {
						cfindi.find(".tp-number-special").addClass("tp-pass-success-ind");
						cfindi.find(".tp-number-special i").removeClass("fa-question-circle").addClass("fa-check-circle");
						strength++;
					} else {
						cfindi.find(".tp-number-special").removeClass("tp-pass-success-ind");
						cfindi.find(".tp-number-special i").addClass("fa-question-circle").removeClass("fa-check-circle");
					}
				}
				
				if((pat1.length && strength ==4) || (pat2.length && strength ==2) || (pat3.length && strength ==3 || pat4.length && strength ==3 || pat5.length && strength ==3)){					
					cfclicki.addClass('tp-done');
					$(this).closest(".tp-user-register").find(".tp-pass-indicator").addClass('tp-done');
					setTimeout(function(){ cfclicki.fadeOut(400); }, 1000);
					$(this).closest(".tp-user-register").find("button.tp-button").removeAttr( "disabled" );
				}else{
					cfclicki.removeClass('tp-done');
					$(this).closest(".tp-user-register").find(".tp-pass-indicator").removeClass('tp-done');
					setTimeout(function(){ cfclicki.fadeIn(400); }, 1000);
					$(this).closest(".tp-user-register").find("button.tp-button").attr("disabled", true);
				}
				
				if (password == false) {
					$(this).closest(".tp-user-register").find("button.tp-button").attr("disabled", true);
				}
			});
			
		}		
	};	
	
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-wp-login-register.default', WidgetUnfoldHandler);
	});
})(jQuery);