/**
 * Porto Dimension
 * 
 * @since 6.1.0
 */
jQuery('.porto-wpb-dimension-container .porto-responsive-toggle').on('click', function (e) {
    var $this = jQuery(this);
    $this.parent().toggleClass('show');
});

if (undefined == js_porto_admin_vars || undefined == js_porto_admin_vars.porto_dimension_included || true != js_porto_admin_vars.porto_dimension_included) {
    jQuery(document.body).on('click', '.porto-wpb-dimension-container .porto-responsive-span li', function (e) {
        var $this = jQuery(this),
            $dropdown = $this.closest('.porto-responsive-dropdown'),
            $toggle = $dropdown.find('.porto-responsive-toggle'),
            $control = $dropdown.parent(),
            $inputs = $control.find('.porto-wpb-dimension');
        // Actions
        $this.addClass('active').siblings().removeClass('active');
        $dropdown.removeClass('show');
        $toggle.html($this.html());

        // Trigger
        var $sizeControl = jQuery('#vc_screen-size-control'),
            $uiPanel = $this.closest('.vc_ui-panel-window');
        if ($sizeControl.length > 0) {
            $sizeControl.find('[data-size="' + $this.data('size') + '"]').click();
        }
        if ($uiPanel.length > 0) {
            $uiPanel.find('.porto-responsive-span [data-width="' + $this.data('width') + '"]').trigger('responsive_changed');
        }

        // Responsive Data
        var width = $this.data('width');
        $control.data('width', width);
        $inputs.each(function () {
            var $input = jQuery(this);
            $input.val($input.data(width) ? $input.data(width) : '');
        });
    }).off('responsive_changed', '.porto-wpb-dimension-container .porto-responsive-span li').on('responsive_changed', '.porto-wpb-dimension-container .porto-responsive-span li', function (e) {
        var $this = jQuery(this),
            $dropdown = $this.closest('.porto-responsive-dropdown'),
            $toggle = $dropdown.find('.porto-responsive-toggle'),
            $control = $dropdown.parent(),
            $inputs = $control.find('.porto-wpb-dimension');

        // Actions
        $this.addClass('active').siblings().removeClass('active');
        $dropdown.removeClass('show');
        $toggle.html($this.html());

        // Responsive Data
        var width = $this.data('width');
        $control.data('width', width);
        $inputs.each(function () {
            var $input = jQuery(this);
            $input.val($input.data(width) ? $input.data(width) : '');
        });
    }).on('change', '.porto-wpb-dimension', function (e) {
        var $this = jQuery(this),
            $control = $this.closest('.porto-wpb-dimension-container'),
            $form = $control.next();
        if (undefined == $control.data('width')) {
            $this.data('xl', $this.val());
        } else {
            $this.data($control.data('width'), $this.val());
        }
        var data = {
            top: $control.find('.top input').data(),
            right: $control.find('.right input').data(),
            bottom: $control.find('.bottom input').data(),
            left: $control.find('.left input').data()
        };
        // Set Data
        $form.val(JSON.stringify(data));
    });
    if (undefined == js_porto_admin_vars) {
        js_porto_admin_vars = {
            porto_dimension_included: true,
        }
    } else {
        js_porto_admin_vars.porto_dimension_included = true;
    }
}