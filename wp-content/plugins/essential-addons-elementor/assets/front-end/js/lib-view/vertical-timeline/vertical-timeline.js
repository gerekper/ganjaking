jQuery(document).ready(function($){
	var timelineBlocks = $('.eael-content-timeline-block'),
		offset = 0.8;

	//hide timeline blocks which are outside the viewport
	hideBlocks(timelineBlocks, offset);

	//on scolling, show/animate timeline blocks when enter the viewport
	$(window).on('scroll', function(){
		(!window.requestAnimationFrame)
			? setTimeout(function(){ showBlocks(timelineBlocks, offset); }, 100)
			: window.requestAnimationFrame(function(){ showBlocks(timelineBlocks, offset); });
	});

	function hideBlocks(blocks, offset) {
		blocks.each(function(){
			( $(this).offset().top > $(window).scrollTop()+$(window).height()*offset ) && $(this).find('.eael-content-timeline-img, .eael-content-timeline-content').addClass('is-hidden');
		});
	}

	function showBlocks(blocks, offset) {
		blocks.each(function(){
			( $(this).offset().top <= $(window).scrollTop()+$(window).height()*offset && $(this).find('.eael-content-timeline-img').hasClass('is-hidden') ) && $(this).find('.eael-content-timeline-img, .eael-content-timeline-content').removeClass('is-hidden').addClass('');
		});
	}

	/**
	 * Timeline Animation Script
	 */
	var getElementsInArea = (function(docElm){
	    var viewportHeight = docElm.clientHeight;

	    return function(e, opts){
	        var found = [], i;

	        if( e && e.type == 'resize' )
	            viewportHeight = docElm.clientHeight;

	        for( i = opts.elements.length; i--; ){
	            var elm        = opts.elements[i],
	                pos        = elm.getBoundingClientRect(),
	                topPerc    = pos.top    / viewportHeight * 100,
	                bottomPerc = pos.bottom / viewportHeight * 100,
	                middle     = (topPerc + bottomPerc)/2,
	                inViewport = middle > opts.zone[1] &&
	                             middle < (100-opts.zone[1]);

	            elm.classList.toggle(opts.markedClass, inViewport);

	            if( inViewport )
	                found.push(elm);
	        }
	    };
	})(document.documentElement);

	/**
	 * Use Case
	 */
	window.addEventListener('scroll', f)
	window.addEventListener('resize', f)

	function f(e){
	    getElementsInArea(e, {
	        elements    : document.querySelectorAll('.eael-content-timeline-block'),
	        markedClass : 'eael-highlight',
	        zone        : [15, 15] // percentage distance from top & bottom
	    });
	}
});