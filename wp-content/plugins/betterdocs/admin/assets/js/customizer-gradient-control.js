jQuery(document).ready(function($) {
    "use strict";
    $(".betterdocs-gradient-color-control").each(function() {
        let gradient_color = $(this).find('.flexia-gradient-color');
        let color1 = $(this).find('.gradient-control-color-1');
        let color1_percent = $(this).find('.gradient-control-color-1-percent');
        let color2 = $(this).find('.gradient-control-color-2');
        let color2_percent = $(this).find('.gradient-control-color-2-percent');
        let color3 = $(this).find('.gradient-control-color-3');
        let color3_percent = $(this).find('.gradient-control-color-3-percent');
        let color4 = $(this).find('.gradient-control-color-4');
        let color4_percent = $(this).find('.gradient-control-color-4-percent');
        let direction = $(this).find('.gradient-control-direction');
        let angle = $(this).find('.gradient-control-angle');

        let containerColor1 = $(this).find('.customize-control-gradient-color-1');
        let containerColor2 = $(this).find('.customize-control-gradient-color-2');
        let containerColor3 = $(this).find('.customize-control-gradient-color-3');
        let containerColor4 = $(this).find('.customize-control-gradient-color-4');

        let gradient_result = {
            color1: color1.val(),
            color1_percent: color1_percent.val(),
            color2: color2.val(),
            color2_percent: color2_percent.val(),
            color3: color3.val(),
            color3_percent: color3_percent.val(),
            color4: color4.val(),
            color4_percent: color4_percent.val(),
            direction: direction.val(),
            angle: parseInt(angle.val()),
        };

        let color1PickerOptions = {
            change: function() {
                let wpColor1 = color1.wpColorPicker( 'color' );
                gradient_result.color1 = wpColor1;
                gradient_color.val(JSON.stringify(gradient_result)).change();
            }
        }
        color1.wpColorPicker( color1PickerOptions );

        let color2PickerOptions = {
            change: function() {
                let wpColor2 = color2.wpColorPicker( 'color' );
                gradient_result.color2 = wpColor2;
                gradient_color.val(JSON.stringify(gradient_result)).change();
            }
        }
        color2.wpColorPicker( color2PickerOptions );

        let color3PickerOptions = {
            change: function() {
                let wpColor3 = color3.wpColorPicker( 'color' );
                gradient_result.color3 = wpColor3;
                gradient_color.val(JSON.stringify(gradient_result)).change();
            }
        }
        color3.wpColorPicker( color3PickerOptions );
        
        let color4PickerOptions = {
            change: function() {
                let wpColor4 = color4.wpColorPicker( 'color' );
                gradient_result.color4 = wpColor4;
                gradient_color.val(JSON.stringify(gradient_result)).change();
            }
        }
        color4.wpColorPicker( color4PickerOptions );

        $(containerColor1).find( '.button.wp-picker-clear' ).on('click', function () {
            gradient_result.color1 = 'transparent';
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })

        $(containerColor2).find( '.button.wp-picker-clear' ).on('click', function () {
            gradient_result.color2 = 'transparent';
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })

        $(containerColor3).find( '.button.wp-picker-clear' ).on('click', function () {
            gradient_result.color3 = 'transparent';
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })

        $(containerColor4).find( '.button.wp-picker-clear' ).on('click', function () {
            gradient_result.color4 = 'transparent';
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })

        $(color1_percent).bind('change onkeyup', function () {
            let color1_percent_val = color1_percent.val();
            gradient_result.color1_percent = color1_percent_val;
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })

        $(color2_percent).bind('change onkeyup', function () {
            let color2_percent_val = color2_percent.val();
            gradient_result.color2_percent = color2_percent_val;
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })
        $(color3_percent).bind('change onkeyup', function () {
            let color3_percent_val = color3_percent.val();
            gradient_result.color3_percent = color3_percent_val;
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })
        $(color4_percent).bind('change onkeyup', function () {
            let color4_percent_val = color4_percent.val();
            gradient_result.color4_percent = color4_percent_val;
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })

        $(direction).on('change', function(){
            gradient_result.angle = 0;
            let direction_val = direction.val();
            gradient_result.direction = direction_val;
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })

        $(angle).on('change', function(){
            let angle_val = parseInt(angle.val());
            gradient_result.angle = angle_val;
            gradient_color.val(JSON.stringify(gradient_result)).change();
        })
        
    });
});
