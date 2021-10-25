/*global redux_change, wp, redux*/

(function($){
	"use strict";
	var count_delete_btn = 0;
	redux.field_objects = redux.field_objects || {};
	redux.field_objects.slides2 = redux.field_objects.slides2 || {};

	$(document).ready(
			function(){
				redux.field_objects.slides2.init();
			}
	);
	redux.field_objects.slides2.removeFirstDeleteBtn = function(parentobj){
		parentobj.find(".redux-field").each(function(){
			var slide_del;
			slide_del = $(this).find(".redux-slides-remove");
			slide_del.remove();
			return false;
		});
	};
	redux.field_objects.slides2.countDeleteBtn = function(parentobj){
		count_delete_btn = 0;
		parentobj.find(".redux-field").each(function(){
			var slide_del;
			slide_del = $(this).find(".redux-slides-remove");
			count_delete_btn++;
		});
		return count_delete_btn;
	};
	redux.field_objects.slides2.init = function(selector){

		if(!selector){
			selector = $(document).find(".redux-group-tab").find('.redux-slides-accordion2');
		}
		var parent = selector.parent();
//		console.log(selector);
		$(selector).each(
				function(){
					var el = $(this);

					redux.field_objects.media.init(el);

					var parent = el;
					if(!el.hasClass('redux-field-container')){
						parent = el.parents('.redux-field-container:first');
					}


					if(parent.hasClass('redux-container-slides')){
						parent.addClass('redux-field-init');
					}
					if(redux.field_objects.slides2.countDeleteBtn(parent) <= 1){
						redux.field_objects.slides2.removeFirstDeleteBtn(parent);
					}

					parent.find('.remove-image').on("click", function(){
//						console.log("remove");
						$(this).parentsUntil(".redux-slides-accordion-group").find('.redux-slides-header').text('Adding new Font');
						$(this).parentsUntil(".redux-slides-accordion-group").find('.redux_upload_file_name').text('');
					});
					parent.find('.redux-slides-remove').on(
							'click', function(){
								redux_change($(this));

								$(this).parent().siblings().find('input[type="text"]').val('');
								$(this).parent().siblings().find('textarea').val('');
								$(this).parent().siblings().find('input[type="hidden"]').val('');
								$(this).parents(".redux-slides-accordion-group").remove();
								var count;
								count = redux.field_objects.slides2.countDeleteBtn(parent);
//								console.log(count);
								if(count <= 1){
									redux.field_objects.slides2.removeFirstDeleteBtn(parent);
								}
							}
					);
					//el.find( '.redux-slides-add' ).click(
					parent.find('.redux-slides-add').off('click').click(
							function(){
//								console.log("click");
								var newSlide = $(this).prev().find('.redux-slides-accordion-group:last').clone(true);
//								console.log(newSlide);
								var slideCount = $(newSlide).find('.upload-id').attr("name").match(/[0-9]+(?!.*[0-9])/);
								var slideCount1 = slideCount * 1 + 1;
//								console.log(slideCount1);
//								if(slideCount1 == 1){
//									redux.field_objects.slides2.removeFirstDeleteBtn(parent);
//									console.log("remove first");
//								}
								$(newSlide).find('input[type="text"], input[type="hidden"], textarea').each(
										function(){
//											console.log($(this));
											name = $(this).attr("name");
//											name = name.replace(/[0-9]+(?!.*[0-9])/, slideCount1);
//											console.log(name);
											$(this).attr("name", name.replace(/[0-9]+(?!.*[0-9])/, slideCount1));
//											$(this).attr("id", $(this).attr("id").replace(/[0-9]+(?!.*[0-9])/, slideCount1));
											$(this).val('');
											if($(this).hasClass('slide-sort')){
												$(this).val(slideCount1);
											}
										}
								);
								var remove_btn;
								remove_btn = $(newSlide).find(".redux-slides-remove");
//								console.log(remove_btn);
//								console.log(remove_btn.length);
								if(remove_btn.length == 0){
//									console.log("add remove btn");
									$(newSlide).find(".redux-slides-list li").last().append('<a href="javascript:void(0);" class="button deletion redux-slides-remove">Delete</a>');
								}

								var content_new_title = $(this).prev().data('new-content-title');

								$(newSlide).find('.screenshot').removeAttr('style');
								$(newSlide).find('.screenshot').addClass('hide');
								$(newSlide).find('.screenshot a').attr('href', '');
								$(newSlide).find('.remove-image').addClass('hide');
								$(newSlide).find('.redux-slides-image').attr('src', '').removeAttr('id');
								$(newSlide).find('h3').text('').append('<span class="redux-slides-header">' +
										content_new_title + '</span><span  class="redux_upload_file_name"></span>');
								$(this).prev().append(newSlide);
							}
					);
				}
		);
	};
})(jQuery);
