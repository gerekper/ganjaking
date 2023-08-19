<?php

namespace ACA\ACF;

interface FieldType {

	const TYPE_BUTTON_GROUP = 'button_group';
	const TYPE_BOOLEAN = 'true_false';
	const TYPE_CHECKBOX = 'checkbox';
	const TYPE_CLONE = 'clone';
	const TYPE_COLOR_PICKER = 'color_picker';
	const TYPE_DATE_PICKER = 'date_picker';
	const TYPE_DATE_TIME_PICKER = 'date_time_picker';
	const TYPE_EMAIL = 'email';
	const TYPE_FILE = 'file';
	const TYPE_FLEXIBLE_CONTENT = 'flexible_content';
	const TYPE_GOOGLE_MAP = 'google_map';
	const TYPE_GROUP = 'group';
	const TYPE_GALLERY = 'gallery';
	const TYPE_IMAGE = 'image';
	const TYPE_LINK = 'link';
	const TYPE_NUMBER = 'number';
	const TYPE_OEMBED = 'oembed';
	const TYPE_PAGE_LINK = 'page_link';
	const TYPE_PASSWORD = 'password';
	const TYPE_POST = 'post_object';
	const TYPE_RADIO = 'radio';
	const TYPE_RANGE = 'range';
	const TYPE_REPEATER = 'repeater';
	const TYPE_RELATIONSHIP = 'relationship';
	const TYPE_SELECT = 'select';
	const TYPE_TAXONOMY = 'taxonomy';
	const TYPE_TEXT = 'text';
	const TYPE_TEXTAREA = 'textarea';
	const TYPE_TIME_PICKER = 'time_picker';
	const TYPE_URL = 'url';
	const TYPE_USER = 'user';
	const TYPE_WYSIWYG = 'wysiwyg';
	const TYPE_TAB = 'tab';
	const TYPE_MESSAGE = 'message';
	const TYPE_SEPARATOR = 'separator';
	const TYPE_OUTPUT = 'output';

	// 3rd party
	const TYPE_IMAGE_CROP = 'image_crop';

}