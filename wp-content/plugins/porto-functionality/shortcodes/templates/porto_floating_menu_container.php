<?php

$output = $el_class = '';

extract(
	shortcode_atts(
		array(
			'el_class' => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$output                         .= '<div class="floating-menu ' . esc_attr( $el_class ) . '">';
	$output                     .= '<div class="floating-menu-body">';
		$output                 .= '<div class="floating-menu-row">';
			$output             .= '<button class="btn floating-menu-btn-collapse-nav" data-bs-toggle="collapse" data-bs-target=".floating-menu-nav-main">';
				$output         .= '<i class="fas fa-bars"></i>';
			$output             .= '</button>';
			$output             .= '<div class="floating-menu-nav-main collapse">';
				$output         .= '<nav class="wrapper-spy">';
					$output     .= '<ul class="nav">';
						$output .= do_shortcode( $content );
					$output     .= '</ul>';
				$output         .= '</nav>';
			$output             .= '</div>';
		$output                 .= '</div>';
	$output                     .= '</div>';
$output                         .= '</div>';

echo porto_filter_output( $output );
?>
<script>
jQuery(document).ready(function($) {
	/*
	* Floating Menu Movement
	*/
	var menuFloatingAnim = {
		$menuFloating: $('.floating-menu .floating-menu-body > .floating-menu-row'),

		build: function() {
			var self = this;
			self.init();
		},
		init: function(){
			var self  = this,
				divisor = 0;

			$(window).on('scroll', function() {
				var scrollPercent = 100 * $(window).scrollTop() / ($(document).height() - $(window).height()),
					st = $(this).scrollTop(),
					divisor = $(document).height() / $(window).height();
				if (divisor < 1.5) {
					return;
				}
				var endValue = ($(document).height() - $(window).height()) / divisor,
					offsetPx = st / divisor,
					offsetTop = parseInt(self.$menuFloating.css('top'), 10);
				if (endValue - offsetTop - 40 < self.$menuFloating.height()) {
					offsetPx *= self.$menuFloating.height() / ( endValue - offsetTop - 40 );
				}

				self.$menuFloating.css({
					transform : 'translateY( calc('+ scrollPercent +'vh - '+ offsetPx +'px) )' 
				});
			});
		}
	}

	if( $('.floating-menu').length ) {
		if( $(window).height() > 700 ) {
			menuFloatingAnim.build();
		}
	}
});
</script>
