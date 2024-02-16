/*! WP Ultimate Firewall Admin Scripts */

jQuery(function ($) {
      var $ppc1 = $('.wpufopt'),
        optpercent = parseInt($ppc1.data('optpercent')),
        deg = 360*optpercent/100;
      
	  if (optpercent > 50) {
        $ppc1.addClass('gt-50');
      }
	  
      $('.wpufopt-fill').css('transform','rotate('+ deg +'deg)');
      $('.wpufopt-text span').html(optpercent+'%');
    });

jQuery(function ($) {
	  var $ppc2 = $('.wpufsec'),
        secpercent = parseInt($ppc2.data('secpercent')),
        deg = 360*secpercent/100;
		
	  if (secpercent > 50) {
		$ppc2.addClass('gt-50');
      }     


	  $('.wpufsec-fill').css('transform','rotate('+ deg +'deg)');
      $('.wpufsec-text span').html(secpercent+'%');	  
    });

jQuery(function ($) {
      var $ppc3 = $('.wpufcs'),
        cspercent = parseInt($ppc3.data('cspercent')),
        deg = 360*cspercent/100;

	  if (cspercent > 50) {
		$ppc3.addClass('gt-50');
      } 
	  $('.wpufcs-fill').css('transform','rotate('+ deg +'deg)');
      $('.wpufcs-text span').html(cspercent+'%');
    });
	
jQuery(function ($) {
      var $ppc4 = $('.wpufur'),
        urpercent = parseInt($ppc4.data('urpercent')),
        deg = 360*urpercent/100;

	  if (urpercent > 50) {
		$ppc4.addClass('gt-50');
      } 
	  $('.wpufur-fill').css('transform','rotate('+ deg +'deg)');
      $('.wpufur-text span').html(urpercent+'%');
    });