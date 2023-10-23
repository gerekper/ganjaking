(function() {

  'use strict';

  // define variables
  var items = document.querySelectorAll(".timeline li");

  // check if an element is in viewport
  // http://stackoverflow.com/questions/123999/how-to-tell-if-a-dom-element-is-visible-in-the-current-viewport
  function isElementInViewport(el) {
    var rect = el.getBoundingClientRect();
    var element = jQuery('#timeline');
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
    
//    return (
//    	      rect.top >= 0 &&
//    	      rect.left >= 0 &&
//    	      rect.bottom <= (element.innerHeight()) &&
//    	      rect.right <= (element.innerWidth())
 //   	);
  }

  function callbackFunc() {
    for (var i = 0; i < items.length; i++) {
      if (isElementInViewport(items[i])) {
        items[i].classList.add("in-view");
      }
    }
  }

  // listen for events
//  var timeline = document.querySelectorAll(".timeline ul");
  	window.addEventListener("load", callbackFunc);
//  timeline.addEventListener("resize", callbackFunc);
    window.addEventListener("scroll", callbackFunc);
	// jQuery(document).on('scroll','#timeline',callbackFunc);
//  jQuery(document).ready(function(){
//		jQuery('.timeline ul').on('scroll',function(){
//			  console.log("in scroll");
//			  callbackFunc();
//		  })
//	}); 
  
  //jQuery(document).on('load','#timeline ul',callbackFunc);
  //jQuery(document).on('resize','#timeline ul',callbackFunc);
  //jQuery(document).on('scroll','#timeline ul',callbackFunc);
  
  jQuery('#timeline').on('load',function(){
	  callbackFunc();
  });
  
  jQuery('#timeline').on('scroll',function(){
	  callbackFunc();
  });
})();