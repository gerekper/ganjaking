/**
 * Porto Typography
 *
 * @since 6.1.0
 */
jQuery('.porto-wpb-typography-container .porto-wpb-typography-toggle').on('click', function (e) {
    var $this = jQuery(this);
    $this.parent().toggleClass('show');
    $this.next().slideToggle(300);
});
jQuery(document.body).on('change', '.porto-wpb-typography-container .porto-vc-font-family', function (e) {
    var $this = jQuery(this),
        $control = $this.closest('.porto-wpb-typography-container'),
        $form = $control.next(),
        $variants = $control.find('.porto-vc-font-variants'),
        $status = $control.find('.porto-wpb-typography-toggle p'),
        font = $this.val();


    var data = {
        family: $this.val(),
        variant: $variants.val(),
        font_size: $control.find('.porto-vc-font-size').val(),
        line_height: $control.find('.porto-vc-line-height').val(),
        letter_spacing: $control.find('.porto-vc-letter-spacing').val(),
        text_transform: $control.find('.porto-vc-text-transform').val()
    };

    $form.val(JSON.stringify(data));

    $status.text(data.family + ' | ' + data.variant + ' | ' + data.font_size);
}).on('change', '.porto-wpb-typography-container .porto-vc-font-variants, .porto-wpb-typography-container .porto-vc-font-size, .porto-wpb-typography-container .porto-vc-letter-spacing, .porto-wpb-typography-container .porto-vc-line-height, .porto-wpb-typography-container .porto-vc-text-transform', function (e) {
    var $this = jQuery(this),
        $control = $this.closest('.porto-wpb-typography-container'),
        $status = $control.find('.porto-wpb-typography-toggle p'),
        $form = $control.next();

    var data = {
        family: $control.find('.porto-vc-font-family').val(),
        variant: $control.find('.porto-vc-font-variants').val(),
        font_size: $control.find('.porto-vc-font-size').val(),
        line_height: $control.find('.porto-vc-line-height').val(),
        letter_spacing: $control.find('.porto-vc-letter-spacing').val(),
        text_transform: $control.find('.porto-vc-text-transform').val()
    };

    $form.val(JSON.stringify(data));
    $status.text(data.family + ' | ' + data.variant + ' | ' + data.font_size);
});