(function($) {
$(document).ready(function(e) {
	var id = $('.ult-video').map( function() {
	    return $(this).attr('id');
	}).get();
	var id1 = $('.ultv-video__outer-wrap').map( function() {
	    return $(this).attr('data-iconbg');
	}).get();
	var id2 = $('.ultv-video__outer-wrap').map( function() {
	    return $(this).attr('data-overcolor');
	}).get();
	var id3 = $('.ultv-video__outer-wrap').map( function() {
	    return $(this).attr('data-defaultbg');
	}).get();
	var play = $('.ultv-video__outer-wrap').map( function() {
	    return $(this).attr('data-defaultplay');
	}).get();
	var video = $('.ultv-video').map( function() {
	    return $(this).attr('data-videotype');
	}).get();

		for (var i = id.length - 1; i >= 0; i--) {
			$("#"+id[i]+ " .ultv-video").find(' .ultv-video__outer-wrap').css('color',id1[i]);
			$("#"+id[i]+ " .ultv-video").find(' .ultv-youtube-icon-bg').css({ fill: id3[i]});
			$("#"+id[i]+ " .ultv-video").find(' .ultv-vimeo-icon-bg').css({ fill: id3[i]});
			var styleElem = document.head.appendChild(document.createElement("style"));
			styleElem.innerHTML = "#"+id[i]+ " .ultv-video .ultv-video__outer-wrap:before {background: "+id2[i]+";}";
	}
	for( var j = 0; j <= play.length - 1; j++){
		if('icon' == play[j])
		{
			$(".ultv-video").find(" .ultv-video__outer-wrap").hover(function(){
			var $this =$(this);
			$this.css('color',$this.data('hoverbg'));},
			function(){
		      var $this = $(this);
		      $this.css('color', $this.data('iconbg'));
	  		});

		}
		else if( 'defaulticon' == play[j] )
		{
			if( 'uv_iframe' == video[j] )
			{
				 $(".ultv-video").find(" .ultv-video__outer-wrap").hover(function(){
				var $this = $(this);
				$this.find(' .ultv-youtube-icon-bg').css({ fill: $this.data('defaulthoverbg') });},
				function(){
			      var $this = $(this);
			   	$this.find(' .ultv-youtube-icon-bg').css({ fill: $this.data('defaultbg') });
		  		});
			}

			else if( 'vimeo_video' == video[j] )
			{
				$(".ultv-video").find(" .ultv-video__outer-wrap").hover(function(){
				var $this = $(this);
				$this.find(' .ultv-vimeo-icon-bg').css({ fill: $this.data('defaulthoverbg') });},
				function(){
			      var $this = $(this);
			    $this.find(' .ultv-vimeo-icon-bg').css({ fill: $this.data('defaultbg') });
		  		});
			}
		}
	}
		ultvideo();
		$(window).resize(function(e){
			ultvideo();
		});
	});
	function ultvideo(){
		$('.ult-video').each(function(){ 
			this.nodeClass  = "." +$(this).attr('id');
			var outer_wrap = jQuery(this.nodeClass).find('.ultv-video__outer-wrap');

				outer_wrap.off( 'click' ).on( 'click', function( e ) {
					var selector = $( this ).find( '.ultv-video__play' );
					ultvideo_play( selector );
				});
			if( '1' == outer_wrap.data( 'autoplay' ) || true == outer_wrap.data( 'device' ) ) {
		      ultvideo_play( jQuery(this.nodeClass).find( '.ultv-video__play' ) );
		    }
		});
	}
	function ultvideo_play( selector ){
		var iframe 		= $( "<iframe/>" );
      	var vurl 		= selector.data( 'src' );
      	if ( 0 == selector.find( 'iframe' ).length ) {

      	iframe.attr( 'src', vurl );
		    iframe.attr( 'frameborder', '0' );
		    iframe.attr( 'allowfullscreen', '1' );
		    iframe.attr( 'allow', 'autoplay;encrypted-media;' );

		    selector.html( iframe );
      }
      selector.closest( '.ultv-video__outer-wrap' ).find( '.ultv-vimeo-wrap' ).hide();
  	}

})(jQuery);