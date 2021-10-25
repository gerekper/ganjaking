/*
* Master Comments: Frontend Script for 
*/
; (function ($) {
    "use strict";

	jQuery(document).ready(function($) {
		"use strict"; 


		function jltma_setCookie(cname, cvalue, exdays) {
	        var d = new Date();
	        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	        var expires = "expires=" + d.toUTCString();
	        document.cookie = cname + "=" + cvalue + "; " + expires;
	    }

	    function jltma_get_Cookies(cname) {
	        var name = cname + "=";
	        var ca = document.cookie.split(';');
	         for (var i = 0; i < ca.length; i++) {
	            var c = ca[i];
	            while (c.charAt(0) == ' ') {
	                c = c.substring(1);
	            }
	            if (c.indexOf(name) == 0) {
	                return c.substring(name.length, c.length);
	            }
	        }
	        return "";
	    }

	    function jltma_deleteCookie(name) {
	        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
	    }

		function like_dislike_Cookies(trigger_type, comment_id, $this, not_trigger_type) {
	        var jltma_like_cookie = jltma_get_Cookies('jltma_like_' + comment_id);
	        var jltma_dislike_cookie = jltma_get_Cookies('jltma_dislike_' + comment_id);

	        if ($this.hasClass('jltma-like-trigger')) {
	            $('.jltma-dislike-trigger', $this.closest(".jltma-like-dislike-wrapper")).removeClass("jltma-already-disliked");
	            $this.addClass("jltma-already-liked");
	        }

	        if ($this.hasClass('jltma-dislike-trigger')) {
	            $('.jltma-like-trigger', $this.closest(".jltma-like-dislike-wrapper")).removeClass("jltma-already-liked");
	            $this.addClass("jltma-already-disliked");
	        }

	        $.ajax({
	            type: 'post',
	            url: jltma_localize_comments_data.ajax_url,
	            data: {
	                comment_id: comment_id,
	                jltma_like_cookie: jltma_like_cookie,
	                jltma_dislike_cookie:jltma_dislike_cookie,
	                action: 'jltma_like_dislike',
	                type: trigger_type,
	                _wpnonce: jltma_localize_comments_data.ajax_nonce
	            },
	            beforeSend: function (xhr) {
	            },
	            success: function (res) {
	                res = $.parseJSON(res);
	                var cookie_name = 'jltma_' + trigger_type + '_' + comment_id;
	                var del_cookie = 'jltma_' + not_trigger_type + '_' + comment_id;
	                jltma_deleteCookie(del_cookie);
	                jltma_setCookie(cookie_name, 1, 365);
	                var latest_like_count = res.latest_like_count;
	                var latest_dislike_count = res.latest_dislike_count;
	                $('#jltma-like-count-' + comment_id).html(latest_like_count);
	                $('#jltma-dislike-count-' + comment_id).html(latest_dislike_count);
	            }
	        });
	    }

	    $('body').on('click', '.jltma-like-dislike-trigger', function(e) {
	    	e.preventDefault();

	        var $this = $(this), 
	        	comment_id = $(this).data('comment-id'),
	        	trigger_type = $(this).data('trigger-type'),
	        	jltma_like_cookie = jltma_get_Cookies('jltma_like_' + comment_id),
	        	jltma_dislike_cookie = jltma_get_Cookies('jltma_dislike_' + comment_id),
	        	current_like_count = $this.closest('.jltma-like-dislike-wrapper').find('.jltma-like-count-wrap').html(),
	        	current_dislike_count = $this.closest('.jltma-like-dislike-wrapper').find('.jltma-dislike-count-wrap').html();


	        //when clicked on like
	        if(trigger_type == 'like' && jltma_like_cookie == '' ){
	            if (!$this.hasClass('jltma-dislike-trigger')) {
	                var cookie_name = 'jltma_' + trigger_type + '_' + comment_id;
	                jltma_setCookie(cookie_name, 1, 365);
	                if (jltma_dislike_cookie == '') {
	                    var end_character = current_like_count.slice(-1);
	                    if(end_character=="K" || end_character=="M" || end_character=="B" || end_character=="T")
	                    {
	                        var new_like_count = parseInt(current_like_count);
	                        var new_dislike_count = parseInt(current_dislike_count);
	                    }
	                    else{
	                        var new_like_count = parseInt(current_like_count) + 1;
	                        var new_dislike_count = parseInt(current_dislike_count);
	                    }
	                }
	                if (jltma_dislike_cookie != '') {
	                    var end_character = current_like_count.slice(-1);
	                    if(end_character=="K" || end_character=="M" || end_character=="B" || end_character=="T")
	                    {
	                        var new_like_count = parseInt(current_like_count);
	                        var new_dislike_count = parseInt(current_dislike_count);
	                    }
	                    else{
	                        var new_dislike_count = parseInt(current_dislike_count) - 1;
	                        if (new_dislike_count < 0){
	                            new_dislike_count = 0;
	                        }
	                        var new_like_count = parseInt(current_like_count) + 1;
	                    }
	                }
	                $('#jltma-like-count-' + comment_id).html(new_like_count);
	                $('#jltma-dislike-count-' + comment_id).html(new_dislike_count);
	                var not_trigger_type = 'dislike';
	                like_dislike_Cookies(trigger_type, comment_id, $this, not_trigger_type);
	            }
	        }
	        //when clicked on dislike
	        if (jltma_dislike_cookie == '' && trigger_type == 'dislike') {
	            var cookie_name = 'jltma_' + trigger_type + '_' + comment_id;
	            jltma_setCookie(cookie_name, 1, 365);
	            if (!$this.hasClass('jltma-like-trigger')) {
	                if (jltma_like_cookie == '') {
	                    var end_character = current_dislike_count.slice(-1);
	                    if(end_character=="K" || end_character=="M" || end_character=="B" || end_character=="T")
	                    {
	                        var new_like_count = parseInt(current_like_count);
	                        var new_dislike_count = parseInt(current_dislike_count);
	                    }else{
	                        var new_dislike_count = parseInt(current_dislike_count) + 1;
	                        var new_like_count = parseInt(current_like_count);
	                    }
	                }
	                if (jltma_like_cookie != '') {
	                    var end_character = current_dislike_count.slice(-1);
	                    if(end_character=="K" || end_character=="M" || end_character=="B" || end_character=="T")
	                    {
	                        var new_like_count = parseInt(current_like_count);
	                        var new_dislike_count = parseInt(current_dislike_count);
	                    }else{
	                        var new_like_count = parseInt(current_like_count) - 1;
	                        if (new_like_count < 0){
	                            new_like_count = 0;
	                        }
	                        var new_dislike_count = parseInt(current_dislike_count) + 1;
	                    }
	                }
	                $('#jltma-like-count-' + comment_id).html(new_like_count);
	                $('#jltma-dislike-count-' + comment_id).html(new_dislike_count);
	                var not_trigger_type = 'like';
	                like_dislike_Cookies(trigger_type, comment_id, $this, not_trigger_type);
	            }
	        }
	    });




		$('.jltma-comments-wrap').on('click', '.jltma-page-link', function (e) {
	        
	        $(this).closest('.jltma-comment-pagination-wrapper').find('.jltma-page-link').removeClass('jltma-current-page');
	        $(this).addClass('jltma-current-page');

	        var post_id = $('#jltma-current-post-id').val(),
	        	page_number = $(this).data('page-number'),
	        	total_page = $(this).data('total-page'),
	        	pagination_type = $(this).data('pagination-type'),
	        	jltma_comment_data = $(this).closest('.jltma-comments-wrap').data("jltma-comment-settings"),
	        	template = jltma_comment_data.template;

	        $.ajax({
	            type: 'post',
	            url: jltma_localize_comments_data.ajax_url,
	            data: {
	                page_number : page_number,
	                template 	: template,
	                post_id 	: post_id,
	                action		: 'jltma_comment_pagination',
	                _wpnonce	: jltma_localize_comments_data.ajax_nonce
	            },
	            cache:false,
	            beforeSend: function (xhr) {
	                $('.jltma-page-number-loader').show();
	            },
	            success: function (res) {

	                $('.jltma-page-number-loader').hide();
	                var pagination_html = pagination_html_function(page_number, total_page, pagination_type);

	                $('.jltma-comment-list-inner').replaceWith(res);
	                $('.jltma-comment-pagination-wrapper').replaceWith(pagination_html);
	                $('html, body').animate({
	                    scrollTop: $(".jltma-comment-listing-wrap").offset().top
	                }, 1000);
	            }
	        });
	    });


	    $('body').on('click', '.jltma-next-page,.jltma-previous-page', function () {
	        var post_id = $('#jltma-current-post-id').val(),
	        	total_page = $(this).data('total-page'),
	        	pagination_type = $(this).data('pagination-type'),
	        	current_page = $(this).closest('.jltma-comment-pagination-wrapper').find('.jltma-current-page').data('page-number'),
	        	next_page = parseInt(current_page) + 1,
	        	previous_page = parseInt(current_page) - 1,
				jltma_comment_data = $(this).closest('.jltma-comments-wrap').data("jltma-comment-settings"),
	        	template = jltma_comment_data.template;


	        	// template = $(this).closest('.jltma-comments-wrap').attr('data-template-demo');

	        if ($(this).hasClass('jltma-previous-page')) {
	            current_page = previous_page;
	        } else {
	            current_page = next_page;
	        }

	        $.ajax({
	            type: 'post',
	            url: jltma_localize_comments_data.ajax_url,
	            data: {
	                page_number : current_page,
	                template 	: template,
	                post_id		: post_id,
	                action		: 'jltma_comment_pagination',
	                 _wpnonce: jltma_localize_comments_data.ajax_nonce
	            },
	            beforeSend: function (xhr) {
	                $('.jltma-page-number-loader').show();
	            },
	            success: function (res) {
	            	// console.log(res);

	                $('.jltma-page-number-loader').hide();
	                var pagination_html = pagination_html_function(current_page, total_page, pagination_type);
	                $('.jltma-comment-list-inner').replaceWith(res);
	                $('.jltma-comment-pagination-wrapper').replaceWith(pagination_html);
	                $('html, body').animate({
	                    scrollTop: $(".jltma-comment-listing-wrap").offset().top
	                }, 1000);
	            }
	        });
	    });

	    function pagination_html_function(page_number, total_page, pagination_type) {
	        var pagination_html 	= '',
	        	current_page 		= page_number,
	        	page_number_loader	= jltma_localize_comments_data.page_number_loader;

	        pagination_html += '<div class="jltma-comment-pagination-wrapper jltma-' + pagination_type + '"> <ul>';

	        if (current_page > 1) {
	            pagination_html += '<li class="jltma-previous-page-wrap">' +
	                '<a href="javascript:void(0);" class="jltma-previous-page" data-total-page="' + total_page + '" data-pagination-type="page-number">' +
	                '<i class="fa fa-angle-left"></i>' +
	                '</a>' +
	                '</li>';
	        }
	        var lower_limit = current_page;
	        var upper_limit = total_page;

	        for (var page_count = lower_limit; page_count <= upper_limit; page_count++) {
	            var page_class = (current_page == page_count) ? 'jltma-current-page jltma-page-link' : 'jltma-page-link';
	            pagination_html += '<li class="jltma-fa">' +
	                '<a href="javascript:void(0);" data-total-page="' + total_page + '" data-page-number="' + page_count + '" class="' + page_class + '" data-pagination-type="page-number" >' +
	                page_count 
	                '</a>' +
	                '</li>';
	        }

	        if (current_page < total_page) {
	            pagination_html += '<li class="jltma-next-page-wrap">' +
	                '<a href="javascript:void(0);" data-total-page="' + total_page + '" class="jltma-next-page"' + '" data-pagination-type="' + pagination_type + '">' +
	                '<i class="fa fa-angle-right"></i>' +
	                '</a>' +
	                '</li>';
	        }

	        pagination_html += '</ul> <img src="'+ page_number_loader +'" class="jltma-page-number-loader" style="display:none;"></div>';
	        return pagination_html;
	    }




	    $('body').on('click', '.jltma-show-replies-trigger', function () {
	        var comment_id = $(this).data('comment-id');
	        $(this).closest('.jltma-comment-template').siblings("ul").slideDown(400, function () { });
	        $('.jltma-hide-reply-trigger-'+comment_id).show();
	        $('.jltma-show-reply-trigger-'+comment_id).hide();
	    });

	    $('body').on('click', '.jltma-hide-replies-trigger', function () {
	        var comment_id = $(this).data('comment-id');
	        $(this).closest('.jltma-comment-template').siblings("ul").slideUp(400, function () { });
	        $('.jltma-hide-reply-trigger-'+comment_id).hide();
	        $('.jltma-show-reply-trigger-'+comment_id).show();
	    });



		// load more button click event
		$('.jltma_comment_loadmore').click( function(){
			
			var button = $(this);
	 
			// decrease the current comment page value
			jltma_localize_comments_data.jc_page--;
	 
			$.ajax({
				url : jltma_localize_comments_data.ajax_url, 
				data : {
					'action'	: 'jltma_loadmore_comments', 					// action
					'post_id'	: jltma_localize_comments_data.parent_post_id, 	// the current post
					'jc_page' 	: jltma_localize_comments_data.jc_page, 		// current comment page
				},
				type : 'POST',
				beforeSend : function ( xhr ) {
					button.text('Loading...'); // preloader here
				},
				success : function( data ){
					if( data ) {
						$('.jltma-comment-list').append( data );
						button.text('More comments'); 
						 // if the last page, remove the button
						if ( jltma_localize_comments_data.jc_page == 1 )
							button.remove();
					} else {
						button.remove();
					}
				}
			});
			return false;
		});




	}); //document.ready



})(jQuery);