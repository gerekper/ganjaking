/*---bg imageclip---*/
(function($) {
'use strict';
$(document).ready(function() {
$(".pt-plus-row-imageclip").each(function() {
var data_id= $(this).data('id');
var border_width= $(this).data('border-width');
var border_style= $(this).data('border-style');
var border_color= $(this).data('border-color');
var box_shadow=$(this).data('box-shadow');
$('head').append('<style >.'+data_id+' .segmenter__shadow{border-width:'+border_width+';border-style:'+border_style+';border-color:'+border_color+';box-shadow:'+box_shadow+';}</style>');
});
});
})(jQuery);
/*---bg imageclip---*/