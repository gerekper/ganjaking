/**
 * Porto Number
 * 
 * @since 6.1.0
 */
jQuery('.porto-wpb-number-container .porto-responsive-toggle').on('click', function (e) {
    var $this = jQuery(this);
    $this.parent().toggleClass('show');
});

if (undefined == js_porto_admin_vars || undefined == js_porto_admin_vars.porto_number_included || true != js_porto_admin_vars.porto_number_included) {
    jQuery(document.body).on('click', '.porto-wpb-number-container .porto-responsive-span li', function (e) {
        var $this = jQuery(this),
            $dropdown = $this.closest('.porto-responsive-dropdown'),
            $toggle = $dropdown.find('.porto-responsive-toggle'),
            $control = $dropdown.parent(),
            $input = $control.find('.porto-wpb-number');
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
        $input.val($input.data(width) ? $input.data(width) : '');
    }).off('responsive_changed', '.porto-wpb-number-container .porto-responsive-span li').on('responsive_changed', '.porto-wpb-number-container .porto-responsive-span li', function (e) {
        var $this = jQuery(this),
            $dropdown = $this.closest('.porto-responsive-dropdown'),
            $toggle = $dropdown.find('.porto-responsive-toggle'),
            $control = $dropdown.parent(),
            $input = $control.find('.porto-wpb-number');
        // Actions
        $this.addClass('active').siblings().removeClass('active');
        $dropdown.removeClass('show');
        $toggle.html($this.html());

        // Responsive Data
        var width = $this.data('width');
        $control.data('width', width);
        $input.val($input.data(width) ? $input.data(width) : '');
    }).on('change', '.porto-wpb-number', function (e) {
        var $this = jQuery(this),
            $control = $this.parent(),
            $form = $control.next();
        if (undefined == $control.data('width')) {
            $this.data('xl', $this.val());
        } else {
            $this.data($control.data('width'), $this.val());
        }

        // Set Data
        if ($this.hasClass('simple-value')) {
            $form.val($this.val());
        } else {
            $form.val(JSON.stringify($this.data()));
        }
    }).on('change', '.porto-wpb-units', function (e) {
        var $this = jQuery(this),
            $control = $this.parent(),
            $form = $control.next(),
            $input = $control.find('.porto-wpb-number');
        $input.data('unit', $this.val());

        // Set Data
        $form.val(JSON.stringify($input.data()));
    });
    if (undefined == js_porto_admin_vars) {
        js_porto_admin_vars = {
            porto_number_included: true,
        }
    } else {
        js_porto_admin_vars.porto_number_included = true;
    }
}