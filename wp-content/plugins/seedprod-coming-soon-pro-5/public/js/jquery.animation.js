"use strict";

(function (jQuery) {
  'use strict'; // Add/remove the mobile title class, depending on available space and title length

  jQuery.fn.seedprod_responsive_title_shortcode = function () {
    jQuery(this).each(function () {
      var $titleWrapper = jQuery(this),
          $title = $titleWrapper.find('h1, h2, h3, h4, h5, h6'),
          $titleMinWidth = $title.data('min-width') ? $title.data('min-width') : $title.outerWidth(),
          $wrappingParent = $titleWrapper.parent(),
          $wrappingParentWidth = $titleWrapper.parents('.slide-content').length ? $wrappingParent.width() : $wrappingParent.outerWidth(),
          $textsWrapper = $titleWrapper.find('.sp-animated-texts-wrapper'),
          $loop = $titleWrapper.hasClass("sp-loop-on"),
          $width = 0,
          $highlightEffects = {
        circle: ['M325,18C228.7-8.3,118.5,8.3,78,21C22.4,38.4,4.6,54.6,5.6,77.6c1.4,32.4,52.2,54,142.6,63.7 c66.2,7.1,212.2,7.5,273.5-8.3c64.4-16.6,104.3-57.6,33.8-98.2C386.7-4.9,179.4-1.4,126.3,20.7'],
        underline_zigzag: ['M11,126.5c62-5.9,124.1-9.8,186.3-11.8c62.2-1.9,124.5-1.8,186.7,0.2c35,1.2,70.1,3,105,5.4c3.2,0.2,3.2-4.7,0-5 c-42.9-3.4-86-4.4-129-3c-42.9,1.4-85.7,5.2-128.2,11.4c-23.9,3.5-47.7,7.7-71.3,12.7c-2.8,0.6-2.1,4.8,0.7,4.9 c41.2,1.3,82.4,2.6,123.5,3.8c11.7,0.4,23.4,0.7,35.2,1.1c3.2,0.1,3.2-4.9,0-5c-41.2-1.3-82.4-2.6-123.5-3.8 c-11.7-0.4-23.4-0.7-35.2-1.1c0.2,1.6,0.4,3.3,0.7,4.9c41.9-8.8,84.3-15.3,126.9-19.3c42.5-4,85.3-5.7,128-5 c24.1,0.4,48.3,1.6,72.3,3.5c0-1.7,0-3.3,0-5c-62.1-4.3-124.3-6.7-186.5-7c-62.2-0.3-124.5,1.3-186.6,5c-35,2.1-70,4.8-104.9,8.1 C7.8,121.8,7.8,126.8,11,126.5L11,126.5z'],
        x: ['M497.4,23.9C301.6,40,155.9,80.6,4,144.4', 'M14.1,27.6c204.5,20.3,393.8,74,467.3,111.7'],
        strikethrough: ['M3,75h493.5'],
        curly: ['M3,146.1c17.1-8.8,33.5-17.8,51.4-17.8c15.6,0,17.1,18.1,30.2,18.1c22.9,0,36-18.6,53.9-18.6 c17.1,0,21.3,18.5,37.5,18.5c21.3,0,31.8-18.6,49-18.6c22.1,0,18.8,18.8,36.8,18.8c18.8,0,37.5-18.6,49-18.6c20.4,0,17.1,19,36.8,19 c22.9,0,36.8-20.6,54.7-18.6c17.7,1.4,7.1,19.5,33.5,18.8c17.1,0,47.2-6.5,61.1-15.6'],
        diagonal: ['M13.5,15.5c131,13.7,289.3,55.5,475,125.5'],
        double: ['M8.4,143.1c14.2-8,97.6-8.8,200.6-9.2c122.3-0.4,287.5,7.2,287.5,7.2', 'M8,19.4c72.3-5.3,162-7.8,216-7.8c54,0,136.2,0,267,7.8'],
        double_underline: ['M5,125.4c30.5-3.8,137.9-7.6,177.3-7.6c117.2,0,252.2,4.7,312.7,7.6', 'M26.9,143.8c55.1-6.1,126-6.3,162.2-6.1c46.5,0.2,203.9,3.2,268.9,6.4'],
        underline: ['M7.7,145.6C109,125,299.9,116.2,401,121.3c42.1,2.2,87.6,11.8,87.3,25.7']
      },
          $all_paths = '',
          $current_path = '',
          $highlightSVG = '';

      if ((0 === $titleMinWidth || false === $titleMinWidth || '0' === $titleMinWidth) && (0 === $wrappingParentWidth || false === $wrappingParentWidth || '0' === $wrappingParentWidth)) {
        $titleWrapper.removeClass('sp-border-below-title');
      } else if ($titleMinWidth + 100 >= $wrappingParentWidth) {
        $titleWrapper.addClass('sp-border-below-title');
        $title.data('min-width', $titleMinWidth);
      } else {
        $titleWrapper.removeClass('sp-border-below-title');
      }

      if (jQuery($titleWrapper).hasClass('sp-title-rotating')) {
        if (!jQuery($titleWrapper).is('.sp-title-typeIn,.sp-title-clipIn')) {
          jQuery($textsWrapper).find('.sp-animated-text').each(function () {
            var $currentWidth = jQuery(this).width();

            if ($currentWidth > $width) {
              $width = $currentWidth;
            }
          });

          if ('undefined' === typeof $textsWrapper.attr('style') || -1 === $textsWrapper.attr('style').indexOf('width')) {
            $textsWrapper.css('width', $width);
          }
        }

        jQuery($titleWrapper).find('.sp-animated-texts-wrapper').textillate({
          selector: '.sp-animated-texts',
          type: $textsWrapper.attr('data-length'),
          minDisplayTime: $textsWrapper.attr("data-minDisplayTime"),
          loop: $loop
        });
      }
      /*
      if ( jQuery( $titleWrapper ).hasClass( 'sp-title-highlight' ) ) {
      	$all_paths = $highlightEffects[ jQuery( $titleWrapper ).data( 'highlight' ) ];
      	$current_path = jQuery();
      		$all_paths.forEach( function ( $current ) {
      		$current_path = $current_path.add( jQuery( '<path>', { d: $current } ) );
      	} );
      		$highlightSVG = jQuery( '<svg>', {
      		xmlns: 'http://www.w3.org/2000/svg',
      		viewBox: '0 0 500 150',
      		preserveAspectRatio: 'none'
      	} ).html( $current_path );
      		if ( jQuery( $titleWrapper ).data( 'active-highlight' ) !== jQuery( $titleWrapper ).data( 'highlight' ) ) {
      		jQuery( $titleWrapper ).find( '.sp-highlighted-text' ).append( $highlightSVG[ 0 ].outerHTML );
      		$titleWrapper.data( 'active-highlight', jQuery( $titleWrapper ).attr( 'data-highlight' ) );
      	}
      }
      */

    });
  };
})(jQuery);