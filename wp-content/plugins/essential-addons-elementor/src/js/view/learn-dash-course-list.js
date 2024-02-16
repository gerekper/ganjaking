var LearnDash = function ($scope, $) {

    var card = $scope.find('.eael-3d-hover .eael-learn-dash-course'),
        wrap = $scope.find('.eael-learndash-wrapper'),
        $layout_mode = wrap.data('layout-mode');
        hover3D = (wrap.data('3d-hover') !== undefined) ? 'true' : 'false';

    let $nomore_item_text = wrap.data("nomore-item-text");
    let $next_page = wrap.data("next-page");

    if ($layout_mode === 'masonry' && !isEditMode) {
        var $settings = {
            itemSelector: ".eael-learn-dash-course",
            percentPosition: true,
            masonry: {
                columnWidth: ".eael-learn-dash-course"
            }
        };

        // init isotope
        $ld_gallery = $(".eael-learndash-wrapper", $scope).isotope($settings);

        // layout gal, while images are loading
        $ld_gallery.imagesLoaded().progress(function() {
            $ld_gallery.isotope("layout");
        });
    }

    if(hover3D) {
        card.map(function(index, item) {

            $(item).on("mousemove", function(e) {
                var mX = e.clientX,
                    mY = e.clientY,
                    winHalfWidth = window.innerWidth/2,
                    winHalfHeight = window.innerHeight/2 ,
                    xdeg = (mX - winHalfWidth)/winHalfWidth,
                    ydeg = (mY - winHalfHeight)/winHalfHeight;
                
                $(this).css({
                    transition: '0ms',
                    transform: 'rotateX('+(ydeg * 10)+'deg) rotateY('+(xdeg * 10)+'deg)'
                });
            });
    
            window.ondevicemotion = function(event) {
                var acX = event.accelerationIncludingGravity.x,
                    acY = event.accelerationIncludingGravity.y,
                    acZ = event.accelerationIncludingGravity.z,
                    xdeg = acX/5,
                    ydeg = acY/5;
    
                $(this).css({
                    transform: 'rotateX('+(ydeg * 10)+'deg) rotateY('+(xdeg * 10)+'deg)'
                });
            };
    
            $(item).on('mouseout', function(){
                $(this).css({
                    transition: 'transform 300ms linear 0s',
                    transform: 'rotateX(0deg) rotateY(0deg)'
                });
            });
    
        });
    }

    $scope.on("click", ".eael-ld-course-list-load-more", function (e) {
        e.preventDefault();
        $('.eael-learn-dash-course.page-' + $next_page, $scope).removeClass('eael-d-none-lite').addClass('eael-d-block-lite');
        wrap.attr("data-next-page", $next_page + 1);

        $(".eael-learndash-wrapper", $scope).isotope("layout");
        
        if( $('.eael-learn-dash-course.page-' + $next_page, $scope).hasClass('eael-last-ld-course-list-item') ) {
            $(".eael-ld-course-list-load-more", $scope).html( $nomore_item_text ).fadeOut('1500');
        }

        $next_page++;
    });
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-learn-dash-course-list.default",
		LearnDash
	);
});