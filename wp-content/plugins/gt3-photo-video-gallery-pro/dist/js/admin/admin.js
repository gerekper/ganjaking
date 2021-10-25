/*!
 * Gallery Admin JS
 *
 * Copyright 2017, GT3 Theme
 */

function gt3pgInstagramUserID() {
	var $userName, $userID, $ = jQuery, usernameTimer;
	$userName = $(".wpb_vc_param_value[name=user-name]", this.$content);
	$userID = $(".wpb_vc_param_value[name=user-i-d]", this.$content);
	if (!$userName) return;
	$userName.keyup(function () {
		clearTimeout(usernameTimer);
		var userName = this.value.trim();
		if (userName) {
			usernameTimer = setTimeout(getUserIDbyName, 500, userName);
		} else {
			$userID.val('').trigger('change');
		}
	}).trigger("change")

	async function getUserIDbyName(userName) {
		let data = await fetch(`https://www.instagram.com/${userName}/?__a=1`)
			.then((data) => (data.json()))
			.catch(() => (false));

		if (!data) {
			$userID.val('').trigger('change');
			return;
		}
		const {
			graphql: {
				user: {
					id: userID
				}
			}
		} = data;

		$userID.val(userID).trigger('change');
	}

}


jQuery(function ($) {
	"use strict";


	jQuery('.thumbnail-type').on('change', function () {
		if (jQuery('.thumbnail-type').val() == "packery") {
			jQuery(".hidden_gt3pg_thumbnail_type_packery").show();
		} else {
			jQuery(".hidden_gt3pg_thumbnail_type_packery").hide();
		}
		if (jQuery('.thumbnail-type').val() == "slider") {
			jQuery(".hidden_gt3pg_thumbnail_type_slider").show();
		} else {
			jQuery(".hidden_gt3pg_thumbnail_type_slider").hide();
		}
	});

	jQuery('.gt3pg_img_packery_wrap').on('click', function () {
		jQuery('[name="packery"]').val($(this).index()+1).trigger('change');
	});

	jQuery('[name="packery"]').on('change', function () {
		jQuery('.gt3pg_img_packery').removeClass('gt3pg_active');
		jQuery('.gt3pg_img_packery').eq($(this).val()-1).addClass('gt3pg_active');
		// jQuery('.img_packery:nth-child('+$(this).val()+')').addClass('active');
	});


	jQuery('.thumbnail-type').trigger('change');
	jQuery('[name="packery"]').trigger('change').hide();

	$('body').on('click', '#gt3pg-open-video-media', function (e) {
		e.preventDefault();
		var image_frame;
		var button = jQuery('input#gt3pg-open-video-media');
		if (image_frame) {
			image_frame.open();
		}
		// Define image_frame as wp.media object
		image_frame = wp.media({
			title: 'Select Media',
			multiple: false,
			library: {
				type: 'video',
			}
		});

		image_frame.on('close', function () {
			var selection = image_frame.state().get('selection');
			var id = 0;
			var url = '';
			selection.each(function (attachment) {
				url = attachment.attributes.url;
				id = attachment.attributes.id;
			});
			if (url !== '') {
				jQuery('#attachments-' + button.data('post_id') + '-gt3-video-url').attr('value', url).trigger('change');
				button.data('gt3pg_video_id', id);
			}
		});

		image_frame.on('open', function () {
			var selection = image_frame.state().get('selection');
			var ids = button.data('gt3pg_video_id');
			var attachment = wp.media.attachment(ids);
			attachment.fetch();
			selection.add(attachment ? [attachment] : []);
		});

		image_frame.open();
	})
});

