(function($) {
	$.fn.eaelProgressBar = function() {
		var $this = $(this)
		var $layout = $this.data('layout')
		var $num = $this.data('count')
		var $duration = $this.data('duration')

		if($num > 100) {
			$num = 100;
		}

		$this.one('inview', function() {
			if ($layout == 'line' || $layout == 'line_rainbow') {
				$('.eael-progressbar-line-fill', $this).css({
					'width': $num + '%',
				})
			} else if ($layout == 'half_circle' || $layout == 'half_circle_fill') {
				$('.eael-progressbar-circle-half', $this).css({
					'transform': 'rotate(' + ($num * 1.8) + 'deg)',
				})
			} else if ($layout == 'box') {
				$('.eael-progressbar-box-fill', $this).css({
					'height': $num + '%',
				})
			}

			$('.eael-progressbar-count', $this).prop({
				'counter': 0
			}).animate({
				counter: $num
			}, {
				duration: $duration,
				easing: 'linear',
				step: function(counter) {
					if ($layout == 'circle' || $layout == 'circle_fill') {
						var rotate = (counter * 3.6)
						$('.eael-progressbar-circle-half-left', $this).css({
							'transform': "rotate(" + rotate + "deg)",
						})
						if (rotate > 180) {
							$('.eael-progressbar-circle-pie', $this).css({
								'clip-path': 'inset(0)'
							})
							$('.eael-progressbar-circle-half-right', $this).css({
								'visibility': 'visible'
							})
						}
					}

					$(this).text(Math.ceil(counter))
				}
			})
		})
	}
}(jQuery));