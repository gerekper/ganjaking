jQuery(document).ready(function ($) {
    $('.element-pack-notice.is-dismissible .notice-dismiss').on('click', function() {
        $this = $(this).parents('.element-pack-notice');
        var $id = $this.attr('id') || '';
        var $time = $this.attr('dismissible-time') || '';
        var $meta = $this.attr('dismissible-meta') || '';
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'element-pack-notices',
                id: $id,
                meta: $meta,
                time: $time
            }
        });
    });
});