(function ($) {
	'use strict'
	function portoMasonry($wrap) {
		if (!$.fn.isotope) {
			if (!document.getElementById('isotope-js')) {
				let d = document;
				let wf = d.createElement('script'), s = d.scripts[d.scripts.length - 1];
				wf.src = js_porto_vars.theme_url + '/js/libs/isotope.pkgd.min.js';
				wf.id = 'isotope-js'
				s.parentNode.insertBefore(wf, s);
			}
			document.getElementById('isotope-js').addEventListener('load', function () {
				porto_init($wrap);
			})
		} else {
			theme.requestFrame(function() {
				porto_init($wrap);
			});
		}
	}

	window.vcv.on('porto_update', function (id) {
		var $obj = $('#el-' + id);
		if ($obj.hasClass('porto-products-wrap') || $obj.hasClass('porto-products-categories-wrap') || $obj.hasClass('porto-block-wrap')) { // products element
			var $iso_obj = $obj.find('[data-plugin-masonry]');
			if ($iso_obj.length) {
				if ($iso_obj.attr('data-plugin-options')) {
					$iso_obj.data('plugin-options', JSON.parse($iso_obj.attr('data-plugin-options')));
				}
				portoMasonry($obj);
			} else if ($obj.hasClass('porto-block-wrap')) {
				porto_init($obj);
			}
			porto_woocommerce_variations_init($obj);
			porto_woocommerce_init($obj);
		} else if ($obj.hasClass('vce-element-porto-blog') || $obj.hasClass('vce-element-porto-recent-post') || $obj.hasClass('porto-portfolios-wrap') || $obj.hasClass('porto-recent-portfolios-wrap') || $obj.hasClass('vce-element-porto-member') || $obj.hasClass('vce-element-porto-recent-member') || $obj.hasClass('porto-menu-wrap')) { // blog element
			var $iso_obj = $obj.find('[data-plugin-masonry]').length ? $obj.find('[data-plugin-masonry]') : $obj.find('.posts-masonry .posts-container:not(.manual)');
			if (!$iso_obj.length) {
				$iso_obj = $obj.find('.page-members .member-row:not(.manual)');
			}
			if (!$iso_obj.length) {
				$iso_obj = $obj.find('.page-portfolios .portfolio-row:not(.manual)');
			}
			if ($iso_obj.length) {
				if ($iso_obj.attr('data-plugin-options')) {
					$iso_obj.data('plugin-options', JSON.parse($iso_obj.attr('data-plugin-options')));
				}
				portoMasonry($obj);
			} else {
				porto_init($obj);
			}
		}
	});
})(window.jQuery);