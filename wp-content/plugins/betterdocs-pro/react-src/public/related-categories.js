(function ($) {
    'use strict';

    var load_more_button = $('.betterdocs-load-more-wrapper .betterdocs-load-more-button');
    load_more_button.on('click', function (e) {
        e.preventDefault();
        var loadMoreBtn = $(this);
        var loader = $('.betterdocs-load-more-loader');
        var current_terms = $('.betterdocs-related-terms-inner-wrapper').children().length;
        var current_term_id = $(this).data('current_term_id');
        var page = $(this).data('page');

        $.ajax({
            url: betterdocsRelatedTerms.ajax_url,
            type: "GET",
            data: {
                _wpnonce: betterdocsRelatedTerms.nonce,
                action: 'load_more_terms',
                current_term_id,
                page,
                kb_slug: betterdocsRelatedTerms.kb_slug
            },
            beforeSend: () => {
                $('.betterdocs-load-more-button .load-more-text').text('Loading');
                loader.css('display', 'block');
            },
            success: (response) => {
                if (response.data != '') {
                    loadMoreBtn.data('page', page + 1);
                    var payload = $(response.data.html);

                    let timeout = setTimeout(() => {
                        $('.betterdocs-load-more-button .load-more-text').text('Load More');
                        loader.css('display', 'none');
                        $('.betterdocs-related-terms-inner-wrapper').append(payload);
                        payload.css('opacity', 0.0).slideDown('slow').animate({ opacity: 3.0 });

                        if( ! response.data?.has_more_term ) {
                            $('.betterdocs-load-more-wrapper').remove();
                            clearTimeout( timeout );
                        }
                    }, 100);
                }
            }
        });
    })
})(jQuery);
