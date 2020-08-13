/**
 * Script run inside a Customizer control sidebar
 */
(function($) {
    wp.customize.bind('ready', function() {
        rangeSlider();

        var rangeValReset  = jQuery('.betterdocs-customizer-reset.betterdocs-range-value');
        rangeValReset.each(function() {
            $(rangeValReset).on( 'click', function (e) {
                e.preventDefault();
                var nextRangeselector = $(this).next('.betterdcos-range-slider');
                var nextRangeDefaultVal = $(this).next('.betterdcos-range-slider').data('default-val');
                var suffix = nextRangeselector.find('.betterdcos-range-slider__range').attr('suffix');
                nextRangeselector.find('.betterdcos-range-slider__range').val(nextRangeDefaultVal).trigger('change');
                nextRangeselector.find('.betterdcos-range-slider__value').html(nextRangeDefaultVal + suffix);
                
            });
        });
    });

    var rangeSlider = function() {
        var slider = $('.betterdcos-range-slider'),
            range = $('.betterdcos-range-slider__range'),
            value = $('.betterdcos-range-slider__value');

        slider.each(function() {

            value.each(function() {
                var value = $(this).prev().attr('value');
				var suffix = ($(this).prev().attr('suffix')) ? $(this).prev().attr('suffix') : '';
                $(this).html(value + suffix);
            });

            range.on('input', function() {
				var suffix = ($(this).attr('suffix')) ? $(this).attr('suffix') : '';
                $(this).next(value).html(this.value + suffix );
            });
        });
    };

})(jQuery);
