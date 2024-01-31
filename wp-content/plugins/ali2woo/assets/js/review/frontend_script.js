 jQuery(function($) {
		$(".fancybox").fancybox({
				prevEffect : 'none',
				nextEffect : 'none',
				padding: 0,
				closeBtn  : true,
				arrows    : true,
				nextClick : true,

				helpers : {
					thumbs : {
						width  : 50,
						height : 50
					}
				}
		
		});
  });