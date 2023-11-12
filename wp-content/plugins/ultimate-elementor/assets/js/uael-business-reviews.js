( function( $ ) {

	var WidgetUAELReviewsHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var selector = $scope.find( '.uael-reviews-widget-wrapper' );

		if ( selector.length < 1 ) {
			return;
		}

        var layout  = selector.data( 'layout' ),
            skin_type = selector.data( 'review-skin' );

        /* Equal Height code */
        if( 'carousel' == layout || 'bubble' === skin_type ) {
            if( 'carousel' == layout ) {
                var slider_options  = selector.data( 'reviews_slider' );
                selector.slick( slider_options );
            }
            _equal_height( selector );

            selector.on( 'init', function() {
                _equal_height( selector );
            });
        }

        function _equal_height( widget_wrapper ) {

            var equal_height = widget_wrapper.data( 'equal-height' ),
                $parent_wrap = '.uael-review-wrap',
                $child_wrap = '.uael-review';

            if ( 'yes' !== equal_height ) {
                return;
            }

            if( 'bubble' === skin_type ) {
                $parent_wrap = '.uael-review-content-wrap';
                $child_wrap = '.uael-review-content';
            }

            var review_wrapper = widget_wrapper.find( $parent_wrap ),
                max_height = -1,
                wrapper_height = -1,
                box_active_height = -1;

            review_wrapper.each( function( i ) {

                var this_height = $( this ).outerHeight(),
                    blog_post = $( this ).find( $child_wrap ),
                    blog_post_height = blog_post.outerHeight();

                if( max_height < blog_post_height ) {
                    max_height = blog_post_height;
                    box_active_height = max_height + 15;
                }

                if ( wrapper_height < this_height ) {
                    wrapper_height = this_height;
                }
            });

            review_wrapper.each( function( i ) {
                var selector = $( this ).find( $child_wrap );
                selector.animate({ height: max_height }, { duration: 0, easing: 'linear' });
            });

            if( "carousel" == layout && 'bubble' !== skin_type ) {
                widget_wrapper.find('.slick-list.draggable').animate({ height: box_active_height }, { duration: 200, easing: 'linear' });
            }

            max_height = -1;
            wrapper_height = -1;

            review_wrapper.each(function() {

                var $this = jQuery( this ),
                    selector = $this.find( $child_wrap ),
                    blog_post_height = selector.outerHeight();

                if ( $this.hasClass('slick-active') ) {
                    return true;
                }

                selector.css( 'height', blog_post_height );
            });

        }

	};

	$( window ).on( 'elementor/frontend/init', function () {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-business-reviews.default', WidgetUAELReviewsHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-business-reviews.card', WidgetUAELReviewsHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-business-reviews.bubble', WidgetUAELReviewsHandler );

	});

} )( jQuery );
