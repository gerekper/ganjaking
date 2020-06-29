<?php
/**
 * Visual Composer functions
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

/**
 * Shortcodes | Image compatibility
 */

if (! function_exists('mfn_vc_image')) {
	function mfn_vc_image($image = false)
	{
		if ($image && is_numeric($image)) {
			$image = wp_get_attachment_image_src($image, 'full');
			$image = $image[0];
		}
		return $image;
	}
}

/**
 * Shortcodes | Map
 */

add_action('vc_before_init', 'mfn_vc_integrateWithVC');
if (! function_exists('mfn_vc_integrateWithVC')) {
	function mfn_vc_integrateWithVC()
	{

		// Article Box

		vc_map(array(
			'base' => 'article_box',
			'name' => __('Article Box', 'mfn-opts'),
			'category' => __('Muffin Builder', 'mfn-opts'),
			'icon' => 'mfn-vc-icon-article_box',
			'params' => array(

				array(
					'param_name' => 'image',
					'type' => 'attach_image',
					'heading' => __('Image', 'mfn-opts'),
					'description' => __('Featured Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' => 'slogan',
					'type' => 'textfield',
					'heading' => __('Slogan', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' => 'title',
					'type' => 'textfield',
					'heading' => __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' => 'link',
					'type' => 'textfield',
					'heading' => __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' => 'target',
					'type' => 'dropdown',
					'heading'	=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'	=> array_flip(array(
						'' => 'Default | _self',
						'_blank' => 'New Tab or Window | _blank' ,
						'lightbox' => 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Before After

		vc_map(array(
			'base' => 'before_after',
			'name' => __('Before After', 'mfn-opts'),
			'description' => __('Item do NOT work in Frontend Editor', 'mfn-opts'),
			'category' => __('Muffin Builder', 'mfn-opts'),
			'icon' => 'mfn-vc-icon-before_after',
			'params' => array(

				array(
					'param_name' => 'image_before',
					'type' => 'attach_image',
					'heading' => __('Image | Before', 'mfn-opts'),
					'description' => __('Image width should be no less than the width of a column. Minimum 700px', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' => 'image_after',
					'type' => 'attach_image',
					'heading' => __('Image | After', 'mfn-opts'),
					'description' => __('Both images <b>must have the same size</b>', 'mfn-opts'),
					'admin_label'	=> false,
				),

			)
		));

		// Blockquote

		vc_map(array(
			'base' => 'blockquote',
			'name' => __('Blockquote', 'mfn-opts'),
			'category' => __('Muffin Builder', 'mfn-opts'),
			'icon' => 'mfn-vc-icon-blockquote',
			'params' => array(

				array(
					'param_name' => 'content',
					'type' => 'textarea',
					'heading' => __('Content', 'mfn-opts'),
					'admin_label'	=> true,
					'value' => __('Insert your content here', 'mfn-opts'),
				),

				array(
					'param_name' => 'author',
					'type' => 'textfield',
					'heading' => __('Author', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' => 'link',
					'type' => 'textfield',
					'heading' => __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' => 'target',
					'type' => 'dropdown',
					'heading' => __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value' => array_flip(array(
						'' => 'Default | _self',
						'_blank' => 'New Tab or Window | _blank' ,
						'lightbox' => 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Blog

		vc_map(array(
			'base' => 'blog',
			'name' => __('Blog', 'mfn-opts'),
			'description' => __('Recommended column size: 1/1', 'mfn-opts'),
			'category' => __('Muffin Builder', 'mfn-opts'),
			'icon' => 'mfn-vc-icon-blog',
			'params' => array(

				array(
					'param_name' => 'count',
					'type' => 'textfield',
					'heading' => __('Count', 'mfn-opts'),
					'description' => __('Number of posts to show', 'mfn-opts'),
					'admin_label'	=> true,
					'value'	=> 2,
				),

				array(
					'param_name' => 'style',
					'type' => 'dropdown',
					'heading' => __('Style', 'mfn-opts'),
					'admin_label'	=> true,
					'value' => array_flip(array(
						'classic'	=> 'Classic',
						'grid' => 'Grid',
						'masonry'	=> 'Masonry Blog Style',
						'masonry tiles'	=> 'Masonry Tiles',
						'photo'	=> 'Photo (Horizontal Images)',
						'timeline' => 'Timeline',

					)),
				),

				array(
					'param_name' => 'columns',
					'type' => 'dropdown',
					'heading' => __('Columns', 'mfn-opts'),
					'description' => __('Default: 3. Recommended: 2-4. Too large value may crash the layout.<br />This option works in styles: <b>Grid, Masonry</b>', 'mfn-opts'),
					'admin_label'	=> true,
					'value'	=> array_flip(array(
						2	=> 2,
						3	=> 3,
						4	=> 4,
						5	=> 5,
						6	=> 6,
					)),
				),

				array(
					'param_name' => 'category',
					'type' => 'dropdown',
					'heading' => __('Category', 'mfn-opts'),
					'description'	=> __('Select posts category', 'mfn-opts'),
					'admin_label'	=> true,
					'value'	=> array_flip(mfn_get_categories('category')),
				),

				array(
					'param_name' => 'category_multi',
					'type' => 'textfield',
					'heading' => __('Multiple Categories', 'mfn-opts'),
					'description' => __('Categories <b>slugs</b>. Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' => 'exclude_id',
					'type' => 'textfield',
					'heading' => __('Exclude Posts', 'mfn-opts'),
					'description' => __('Posts <b>IDs</b>. IDs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' => 'more',
					'type' => 'dropdown',
					'heading' => __('Show | Read More link', 'mfn-opts'),
					'admin_label'	=> false,
					'value' => array(
						__('No', 'mfn-opts') => 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' => 'filters',
					'type' => 'dropdown',
					'heading' => __('Show | Filters', 'mfn-opts'),
					'description' => __('This option works in <b>Category: All</b> and <b>Style: Masonry</b>', 'mfn-opts'),
					'admin_label'	=> false,
					'value' => array(
						__('No', 'mfn-opts') => 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' => 'pagination',
					'type' => 'dropdown',
					'heading' => __('Show | Pagination', 'mfn-opts'),
					'description'	=> __('<strong>Notice:</strong> Pagination will <strong>not</strong> work if you put item on Homepage of WordPress Multilangual Site.', 'mfn-opts'),
					'admin_label'	=> false,
					'value' => array(
						__('No', 'mfn-opts') => 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' 	=> 'greyscale',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Greyscale Image', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' 	=> 'margin',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Margin', 'mfn-opts'),
					'description' 	=> __('for <b>Style: Masonry Tiles</b> only', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

			)
		));

		// Blog News

		vc_map(array(
			'base' 			=> 'blog_news',
			'name' 			=> __('Blog News', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-blog_news',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> array_flip(array(
						'' 			=> __('Default', 'mfn-opts'),
						'featured'	=> __('Featured 1st', 'mfn-opts'),
					)),
				),

				array(
					'param_name' 	=> 'count',
					'type' 			=> 'textfield',
					'heading' 		=> __('Count', 'mfn-opts'),
					'description' 	=> __('Number of posts to show', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> 2,
				),

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Category', 'mfn-opts'),
					'description' 	=> __('Select posts category', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> array_flip(mfn_get_categories('category')),
				),

				array(
					'param_name' 	=> 'category_multi',
					'type' 			=> 'textfield',
					'heading' 		=> __('Multiple Categories', 'mfn-opts'),
					'description' 	=> __('Slugs should be separated with <strong>coma</strong> (,)', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'excerpt',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Excerpt', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> array_flip(array(
						0 			=> __('Hide', 'mfn-opts'),
						1 			=> __('Show', 'mfn-opts'),
						'featured' 	=> __('Show for Featured only', 'mfn-opts'),
					)),
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Button | Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'link_title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Button | Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

			)
		));

		// Blog Slider

		vc_map(array(
			'base' 			=> 'blog_slider',
			'name' 			=> __('Blog Slider', 'mfn-opts'),
			'description' 	=> __('Item do NOT work in Frontend Editor', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-blog_slider',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'count',
					'type' 			=> 'textfield',
					'heading' 		=> __('Count', 'mfn-opts'),
					'description' 	=> __('Number of posts to show', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> 2,
				),

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Category', 'mfn-opts'),
					'description' 	=> __('Select posts category', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> array_flip(mfn_get_categories('category')),
				),

				array(
					'param_name' 	=> 'category_multi',
					'type' 			=> 'textfield',
					'heading' 		=> __('Multiple Categories', 'mfn-opts'),
					'description' 	=> __('Slugs should be separated with <strong>coma</strong> (,)', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name'	=> 'more',
					'type'			=> 'dropdown',
					'heading'		=> __('Show Read More button', 'mfn-opts'),
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						''		=> 'Default',
						'flat' 	=> 'Flat',
					)),
				),

				array(
					'param_name' 	=> 'navigation',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Navigation', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						''				=> __('Default', 'mfn-opts'),
						'hide-arrows'	=> __('Hide Arrows', 'mfn-opts'),
						'hide-dots'		=> __('Hide Dots', 'mfn-opts'),
						'hide-nav'		=> __('Hide Navigation', 'mfn-opts'),
					)),
				),

			)
		));

		// Call to Action

		vc_map(array(
			'base' 			=> 'call_to_action',
			'name' 			=> __('Call to Action', 'mfn-opts'),
			'description' 	=> __('Recommended column size: 1/1', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-call_to_action',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'icon',
					'type' 			=> 'textfield',
					'heading' 		=> __('Icon', 'mfn-opts'),
					'description' 	=> __('Font Icon, eg. <strong>icon-lamp</strong>', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> __('Insert your content here', 'mfn-opts'),
				),

				array(
					'param_name' 	=> 'button_title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Button Title', 'mfn-opts'),
					'description'	=> __('Leave this field blank if you want Call to Action with Big Icon', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

				array(
					'param_name' 	=> 'class',
					'type' 			=> 'textfield',
					'heading' 		=> __('Class', 'mfn-opts'),
					'description'	=> __('This option is useful when you want to use <b>scroll</b>', 'mfn-opts'),
					'admin_label'	=> false,
				),

			)
		));

		// Chart

		vc_map(array(
			'base' 			=> 'chart',
			'name' 			=> __('Chart', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-chart',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'percent',
					'type' 			=> 'textfield',
					'heading' 		=> __('Percent', 'mfn-opts'),
					'description' 	=> __('Number between 0-100', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'label',
					'type' 			=> 'textfield',
					'heading' 		=> __('Chart Label', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'icon',
					'type' 			=> 'textfield',
					'heading' 		=> __('Icon', 'mfn-opts'),
					'description' 	=> __('Font Icon, eg. <strong>icon-lamp</strong>', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Chart Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'line_width',
					'type' 			=> 'textfield',
					'heading' 		=> __('Line Width', 'mfn-opts'),
					'description' 	=> __('px (optional)', 'mfn-opts'),
					'admin_label'	=> true,
				),

			)
		));

		// Clients

		vc_map(array(
			'base' 			=> 'clients',
			'name' 			=> __('Clients', 'mfn-opts'),
			'description' 	=> __('Recommended column size: 1/1', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-clients',
			'params' 		=> array(

				array(
					'param_name' 	=> 'in_row',
					'type' 			=> 'textfield',
					'heading' 		=> __('Items in Row', 'mfn-opts'),
					'desc' 			=> __('Number of items in row. Recommended number: 3-6', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> 6,
				),

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'textfield',
					'heading' 		=> __('Category', 'mfn-opts'),
					'desc' 			=> __('Client Category slug', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'orderby',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order by', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'date'			=> 'Date',
						'menu_order' 	=> 'Menu order',
						'title'			=> 'Title',
						'rand'			=> 'Random',
					)),
				),

				array(
					'param_name' 	=> 'order',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'ASC' 	=> 'Ascending',
						'DESC' 	=> 'Descending',
					)),
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						''			=> 'Default',
						'tiles' 	=> 'Tiles',
					)),
				),

				array(
					'param_name'	=> 'greyscale',
					'type'			=> 'dropdown',
					'heading'		=> __('Greyscale Images', 'mfn-opts'),
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

			)
		));

		// Clients Slider

		vc_map(array(
			'base' 			=> 'clients_slider',
			'name' 			=> __('Clients Slider', 'mfn-opts'),
			'description' 	=> __('Item do NOT work in Frontend Editor', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-clients_slider',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'textfield',
					'heading' 		=> __('Category', 'mfn-opts'),
					'desc' 			=> __('Client Category slug', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'orderby',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order by', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						'menu_order' 	=> 'Menu order',
						'date'			=> 'Date',
						'title'			=> 'Title',
						'rand'			=> 'Random',
					)),
				),

				array(
					'param_name' 	=> 'order',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						'ASC' 	=> 'Ascending',
						'DESC' 	=> 'Descending',
					)),
				),

			)
		));

		// Code

		vc_map(array(
			'base' 			=> 'code',
			'name' 			=> __('Code', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-code',
			'params' 		=> array(

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> true,
				),

			)
		));

		// Contact box

		vc_map(array(
			'base' 			=> 'contact_box',
			'name' 			=> __('Contact box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-contact_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'address',
					'type' 			=> 'textarea',
					'heading' 		=> __('Address', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> __('Insert your content here', 'mfn-opts'),
				),

				array(
					'param_name' 	=> 'telephone',
					'type' 			=> 'textfield',
					'heading' 		=> __('Phone', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'telephone_2',
					'type' 			=> 'textfield',
					'heading' 		=> __('Phone 2nd', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'fax',
					'type' 			=> 'textfield',
					'heading' 		=> __('Fax', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'email',
					'type' 			=> 'textfield',
					'heading' 		=> __('Email', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'www',
					'type' 			=> 'textfield',
					'heading' 		=> __('WWW', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Background Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

			)
		));

		// Countdown

		vc_map(array(
			'base' 			=> 'countdown',
			'name' 			=> __('Countdown', 'mfn-opts'),
			'description' 	=> __('Recommended column size: 1/1', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-countdown',
			'params' 		=> array(

				array(
					'param_name' 	=> 'date',
					'type' 			=> 'textfield',
					'heading' 		=> __('Lunch Date', 'mfn-opts'),
					'description'	=> __('Format: 12/30/2017 12:00:00 month/day/year hour:minute:second', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> '12/30/2017 12:00:00',
				),

				array(
					'param_name' 	=> 'timezone',
					'type' 			=> 'dropdown',
					'heading' 		=> __('UTC Timezone', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> array_flip(mfna_utc()),
				),

			)
		));

		// Counter

		vc_map(array(
			'base' 			=> 'counter',
			'name' 			=> __('Counter', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-counter',
			'params' 		=> array(

				array(
					'param_name' 	=> 'icon',
					'type' 			=> 'textfield',
					'heading' 		=> __('Icon', 'mfn-opts'),
					'description' 	=> __('Font Icon, eg. <strong>icon-lamp</strong>', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'color',
					'type' 			=> 'colorpicker',
					'heading' 		=> __('Icon Color', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Chart Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'number',
					'type' 			=> 'textfield',
					'heading' 		=> __('Number', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'prefix',
					'type' 			=> 'textfield',
					'heading' 		=> __('Prefix', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'label',
					'type' 			=> 'textfield',
					'heading' 		=> __('Postfix', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'type',
					'type' 				=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> false,
					'value'				=> array_flip(array(
						'vertical' 		=> __('Vertical', 'mfn-opts'),
						'horizontal'	=> __('Horizontal', 'mfn-opts'),
					)),
				),

			)
		));

		// Fancy Heading

		vc_map(array(
			'base' 			=> 'fancy_heading',
			'name' 			=> __('Fancy Heading', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-fancy_heading',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'h1',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Use H1 tag', 'mfn-opts'),
					'description' 	=> __('Wrap title into H1 instead of H2', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' 	=> 'icon',
					'type' 			=> 'textfield',
					'heading' 		=> __('Icon', 'mfn-opts'),
					'description' 	=> __('Icon Style only. Font Icon, eg. <strong>icon-lamp</strong>', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> 'icon-lamp',
				),

				array(
					'param_name' 	=> 'slogan',
					'type' 			=> 'textfield',
					'heading' 		=> __('Slogan', 'mfn-opts'),
					'description' 	=> __('Line Style only', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> __('Insert your content here', 'mfn-opts'),
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'description' 	=> __('Some fields above work on selected styles.', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'icon'			=> 'Icon',
						'line'			=> 'Line',
						'arrows' 		=> 'Arrows',
					)),
				),

			)
		));

		// Feature List

		vc_map(array(
			'base' 			=> 'feature_list',
			'name' 			=> __('Feature List', 'mfn-opts'),
			'description' 	=> __('Recommended column size: 1/1', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-feature_list',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'description' 	=> __('This field is used as an Item Label in admin panel only.', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' => 'content',
					'type' => 'textarea',
					'heading' => __('Content', 'mfn-opts'),
					'description' =>  __('Please use <strong>[item icon="" title="List item" link="" target=""]</strong> shortcodes.', 'mfn-opts'),
					'admin_label'	=> false,
					'value' => '[item icon="icon-lamp" title="List item" link="" target="" animate=""]',
				),

				array(
					'param_name' 	=> 'columns',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Columns', 'mfn-opts'),
					'description' 	=> __('Default: 4. Recommended: 2-4. Too large value may crash the layout.', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array( 2, 3, 4, 5, 6 ),
				),

			)
		));

		// Flat Box

		vc_map(array(
			'base' 			=> 'flat_box',
			'name' 			=> __('Flat Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-flat_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> __('Insert your content here', 'mfn-opts'),
				),

				array(
					'param_name' 	=> 'icon',
					'type' 			=> 'textfield',
					'heading' 		=> __('Icon', 'mfn-opts'),
					'description' 	=> __('Font Icon, eg. <strong>icon-lamp</strong>', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'icon_image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Icon | Image', 'mfn-opts'),
					'description' 	=> __('You can use image icon instead of font icon', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'background',
					'type' 			=> 'colorpicker',
					'heading' 		=> __('Icon background', 'mfn-opts'),
					'description' 	=> __('Leave this field blank to use Theme Background.', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Helper

		vc_map(array(
			'base' 			=> 'helper',
			'name' 			=> __('Helper', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-helper',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'title1',
					'type' 			=> 'textfield',
					'heading' 		=> __('Item 1 | Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content1',
					'type' 			=> 'textarea',
					'heading' 		=> __('Item 1 | Content', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'link1',
					'type' 			=> 'textfield',
					'heading' 		=> __('Item 1 | Link', 'mfn-opts'),
					'description' 	=> __('Use this field if you want to link to another page instead of showing the content', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target1',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Item 1 | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array( '', '_blank' ),
				),

				array(
					'param_name' 	=> 'title2',
					'type' 			=> 'textfield',
					'heading' 		=> __('Item 2 | Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content2',
					'type' 			=> 'textarea',
					'heading' 		=> __('Item 2 | Content', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'link2',
					'type' 			=> 'textfield',
					'heading' 		=> __('Item 2 | Link', 'mfn-opts'),
					'description' 	=> __('Use this field if you want to link to another page instead of showing the content', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target2',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Item 2 | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array( '', '_blank' ),
				),

			)
		));

		// Hover Box

		vc_map(array(
			'base' 			=> 'hover_box',
			'name' 			=> __('Hover Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-hover_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'image_hover',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Hover Image', 'mfn-opts'),
					'description' 	=> __('Both images must have the same size.', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Hover Color

		vc_map(array(
			'base' 			=> 'hover_color',
			'name' 			=> __('Hover Color', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-hover_color',
			'params' 		=> array(

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'background',
					'type' 			=> 'colorpicker',
					'heading' 		=> __('Background color', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'background_hover',
					'type' 			=> 'colorpicker',
					'heading' 		=> __('Background color | Hover', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'border',
					'type' 			=> 'colorpicker',
					'heading' 		=> __('Border color', 'mfn-opts'),
					'description' 	=> __('optional', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'border_hover',
					'type' 			=> 'colorpicker',
					'heading' 		=> __('Border color | Hover', 'mfn-opts'),
					'description' 	=> __('optional', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'padding',
					'type' 			=> 'textfield',
					'heading' 		=> __('Padding', 'mfn-opts'),
					'description' 	=> __('Use value with <b>px</b> or <b>%</b>. Example: <b>20px</b> or <b>20px 10px 20px 10px</b> or <b>20px 1%</b>', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

				array(
					'param_name' 	=> 'class',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link | Class', 'mfn-opts'),
					'description' 	=> __('This option is useful when you want to use <b>scroll</b>', 'mfn-opts'),
					'admin_label'	=> false,
				),

			)
		));

		// How it Works

		vc_map(array(
			'base' 			=> 'how_it_works',
			'name' 			=> __('How it Works', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-how_it_works',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Background Image', 'mfn-opts'),
					'description' 	=> __('Recommended: Square Image with transparent background.', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'number',
					'type' 			=> 'textfield',
					'heading' 		=> __('Number', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textfield',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'border',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Line', 'mfn-opts'),
					'description' 	=> __('Show right connecting line', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Icon Box

		vc_map(array(
			'base' 			=> 'icon_box',
			'name' 			=> __('Icon Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-icon_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> __('Insert your content here', 'mfn-opts'),
				),

				array(
					'param_name' 	=> 'icon',
					'type' 			=> 'textfield',
					'heading' 		=> __('Icon', 'mfn-opts'),
					'description' 	=> __('Font Icon, eg. <strong>icon-lamp</strong>', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'icon_position',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Icon Position', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'top'	=> 'Top',
						'left'	=> 'Left',
					)),
				),

				array(
					'param_name' 	=> 'border',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Border', 'mfn-opts'),
					'description' 	=> __('Show right border', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

				array(
					'param_name' 	=> 'class',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link | Class', 'mfn-opts'),
					'description' 	=> __('This option is useful when you want to use <b>scroll</b>', 'mfn-opts'),
					'admin_label'	=> true,
				),

			)
		));

		// Info Box

		vc_map(array(
			'base' 			=> 'info_box',
			'name' 			=> __('Info Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-info_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> '<ul><li>list item 1</li><li>list item 2</li></ul>',
				),

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Background Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

			)
		));

		// List

		vc_map(array(
			'base' 			=> 'list',
			'name' 			=> __('List', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-list',
			'params' 		=> array(

				array(
					'param_name' 	=> 'icon',
					'type' 			=> 'textfield',
					'heading' 		=> __('Icon', 'mfn-opts'),
					'description' 	=> __('Font Icon, eg. <strong>icon-lamp</strong>', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> __('Insert your content here', 'mfn-opts'),
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array( '', '_blank' ),
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						1 => 'With background',
						2 => 'Transparent',
						3 => 'Vertical',
						4 => 'Ordered list',
					)),
				),

			)
		));

		// Map Advanced

		vc_map(array(
			'base' 			=> 'map',
			'name' 			=> __('Map Advanced', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-map',
			'params' 		=> array(

				array(
					'param_name' 	=> 'lat',
					'type' 			=> 'textfield',
					'heading' 		=> __('Google Maps Lat', 'mfn-opts'),
					'description' 	=> __('The map will appear only if this field is filled correctly. Example: -33.87', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'lng',
					'type' 			=> 'textfield',
					'heading' 		=> __('Google Maps Lng', 'mfn-opts'),
					'description' 	=> __('The map will appear only if this field is filled correctly. Example: 151.20', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'zoom',
					'type' 			=> 'textfield',
					'heading' 		=> __('Zoom', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> 13,
				),

				array(
					'param_name' 	=> 'height',
					'type' 			=> 'textfield',
					'heading' 		=> __('Height', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> 200,
				),

				// options

				array(
					'param_name' 	=> 'type',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Type', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'ROADMAP' 		=> __('Map', 'mfn-opts'),
						'SATELLITE' 	=> __('Satellite', 'mfn-opts'),
						'HYBRID' 		=> __('Satellite + Map', 'mfn-opts'),
						'TERRAIN' 		=> __('Terrain', 'mfn-opts'),
					)),
				),

				array(
					'param_name' 	=> 'controls',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Controls', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						'' 							=> __('Zoom', 'mfn-opts'),
						'mapType' 					=> __('Map Type', 'mfn-opts'),
						'streetView'				=> __('Street View', 'mfn-opts'),
						'zoom mapType' 				=> __('Zoom & Map Type', 'mfn-opts'),
						'zoom streetView' 			=> __('Zoom & Street View', 'mfn-opts'),
						'mapType streetView' 		=> __('Map Type & Street View', 'mfn-opts'),
						'zoom mapType streetView'	=> __('Zoom, Map Type & Street View', 'mfn-opts'),
						'hide'						=> __('Hide All', 'mfn-opts'),
					)),
				),

				array(
					'param_name' 	=> 'draggable',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Draggable', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						'' 					=> __('Enable', 'mfn-opts'),
						'disable' 			=> __('Disable', 'mfn-opts'),
						'disable-mobile'	=> __('Disable on Mobile', 'mfn-opts'),
					)),
				),

				array(
					'param_name' 	=> 'border',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Border', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						0 	=> __('No', 'mfn-opts'),
						1 	=> __('Yes', 'mfn-opts'),
					)),
				),

				// advanced

				array(
					'param_name' 	=> 'icon',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Marker Icon [.png]', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'latlng',
					'type' 			=> 'textarea',
					'heading' 		=> __('Additional Markers | Lat,Lng,IconURL', 'mfn-opts'),
					'description' 	=> __('Separate Lat,Lang,IconURL[optional] with <b>coma</b> [ , ]<br />Separate multiple Markers with <b>semicolon</b> [ ; ]<br />Example: <b>-33.88,151.21,ICON_URL;-33.89,151.22</b>', 'mfn-opts'),
					'admin_label'	=> false,
				),

				// contact box

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> __('Insert your content here', 'mfn-opts'),
				),

				array(
					'param_name' 	=> 'telephone',
					'type' 			=> 'textfield',
					'heading' 		=> __('Telephone', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'email',
					'type' 			=> 'textfield',
					'heading' 		=> __('Email', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'www',
					'type' 			=> 'textfield',
					'heading' 		=> __('WWW', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'box'	=> __('Contact Box on the map (for full width column/wrap)', 'mfn-opts'),
						'bar'	=> __('Bar at the top', 'mfn-opts'),
					)),
				),

			)
		));

		// Opening Hours

		vc_map(array(
			'base' 			=> 'opening_hours',
			'name' 			=> __('Opening Hours', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-opening_hours',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'description' 	=> __('HTML tags allowed', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> '<ul><li><label>Monday - Saturday</label><span class="h">8am - 4pm</span></li></ul>',
				),

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Background Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

			)
		));

		// Our Team

		vc_map(array(
			'base' 			=> 'our_team',
			'name' 			=> __('Our Team', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-our_team',
			'params' 		=> array(

				array(
					'param_name' 	=> 'heading',
					'type' 			=> 'textfield',
					'heading' 		=> __('Heading', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Photo', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'description' 	=> __('Will also be used as the image alternative text', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'subtitle',
					'type' 			=> 'textfield',
					'heading' 		=> __('Subtitle', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'phone',
					'type' 			=> 'textfield',
					'heading' 		=> __('Phone', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'email',
					'type' 			=> 'textfield',
					'heading' 		=> __('E-mail', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'facebook',
					'type' 			=> 'textfield',
					'heading' 		=> __('Facebook', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'twitter',
					'type' 			=> 'textfield',
					'heading' 		=> __('Twitter', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'linkedin',
					'type' 			=> 'textfield',
					'heading' 		=> __('LinkedIn', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'blockquote',
					'type' 			=> 'textarea',
					'heading' 		=> __('Blockquote', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> array_flip(array(
						'vertical'		=> 'Vertical',
						'circle'		=> 'Circle',
						'horizontal'	=> 'Horizontal 	[1/2 and wider]',
					)),
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Our Team List

		vc_map(array(
			'base' 			=> 'our_team_list',
			'name' 			=> __('Our Team List', 'mfn-opts'),
			'description' 	=> __('Recommended column size: 1/1', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-our_team_list',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Photo', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'description' 	=> __('Will also be used as the image alternative text', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'subtitle',
					'type' 			=> 'textfield',
					'heading' 		=> __('Subtitle', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'phone',
					'type' 			=> 'textfield',
					'heading' 		=> __('Phone', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'blockquote',
					'type' 			=> 'textarea',
					'heading' 		=> __('Blockquote', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'email',
					'type' 			=> 'textfield',
					'heading' 		=> __('E-mail', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'facebook',
					'type' 			=> 'textfield',
					'heading' 		=> __('Facebook', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'twitter',
					'type' 			=> 'textfield',
					'heading' 		=> __('Twitter', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'linkedin',
					'type' 			=> 'textfield',
					'heading' 		=> __('LinkedIn', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Photo Box

		vc_map(array(
			'base' 			=> 'photo_box',
			'name' 			=> __('Photo Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-photo_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'align',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Text Align', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						''		=> 'Center',
						'left'	=> 'Left',
						'right'	=> 'Right',
					)),
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Portfolio

		vc_map(array(
			'base' 			=> 'portfolio',
			'name' 			=> __('Portfolio', 'mfn-opts'),
			'description' 	=> __('Recommended column size: 1/1', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-portfolio',
			'params' 		=> array(

				array(
					'param_name' 	=> 'count',
					'type' 			=> 'textfield',
					'heading' 		=> __('Count', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> 2,
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'flat'				=> 'Flat',
						'grid'				=> 'Grid',
						'masonry'			=> 'Masonry Blog Style',
						'masonry-hover'		=> 'Masonry Hover Description',
						'masonry-minimal'	=> 'Masonry Minimal',
						'masonry-flat'		=> 'Masonry Flat',
						'list'				=> 'List',
						'exposure'			=> 'Exposure',
					)),
				),

				array(
					'param_name' 	=> 'columns',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Columns', 'mfn-opts'),
					'description' 	=> __('Default: 4. Recommended: 2-4. Too large value may crash the layout.', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array( 2, 3, 4, 5, 6 ),
				),

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'textfield',
					'heading' 		=> __('Category', 'mfn-opts'),
					'description' 	=> __('Portfolio Category slug', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'category_multi',
					'type' 			=> 'textfield',
					'heading' 		=> __('Multiple Categories', 'mfn-opts'),
					'description' 	=> __('Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'orderby',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order by', 'mfn-opts'),
					'description' 	=> __('Portfolio items order by column.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'date'			=> 'Date',
						'menu_order' 	=> 'Menu order',
						'title'			=> 'Title',
						'rand'			=> 'Random',
					)),
				),

				array(
					'param_name' 	=> 'order',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order', 'mfn-opts'),
					'description' 	=> __('Portfolio items order.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'DESC' 	=> 'Descending',
						'ASC' 	=> 'Ascending',
					)),
				),

				array(
					'param_name' 	=> 'exclude_id',
					'type' 			=> 'textfield',
					'heading' 		=> __('Exclude Posts', 'mfn-opts'),
					'description' 	=> __('IDs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'related',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Use as Related Projects', 'mfn-opts'),
					'description' 	=> __('Exclude current Project. This option will override Exclude Posts option above', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array(
							__('No', 'mfn-opts') 	=> 0,
							__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' 	=> 'filters',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Show | Filters', 'mfn-opts'),
					'description' 	=> __('This option works in <b>Category: All</b>', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' 	=> 'pagination',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Show | Pagination', 'mfn-opts'),
					'description' 	=> __('<strong>Notice:</strong> Pagination will <strong>not</strong> work if you put item on Homepage of WordPress Multilangual Site.', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array_flip(array(
						'' 	=> 'No',
						1 	=> 'Yes',
					)),
				),

			)
		));

		// Portfolio Grid

		vc_map(array(
			'base' 			=> 'portfolio_grid',
			'name' 			=> __('Portfolio Grid', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-portfolio_grid',
			'params' 		=> array(

				array(
					'param_name' 	=> 'count',
					'type' 			=> 'textfield',
					'heading' 		=> __('Count', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> 4,
				),

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'textfield',
					'heading' 		=> __('Category', 'mfn-opts'),
					'description' 	=> __('Portfolio Category slug', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'category_multi',
					'type' 			=> 'textfield',
					'heading' 		=> __('Multiple Categories', 'mfn-opts'),
					'description' 	=> __('Slugs should be separated with <strong>coma</strong> (,)', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'orderby',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order by', 'mfn-opts'),
					'description' 	=> __('Portfolio items order by column.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'date'			=> 'Date',
						'menu_order' 	=> 'Menu order',
						'title'			=> 'Title',
						'rand'			=> 'Random',
					)),
				),

				array(
					'param_name' 	=> 'order',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order', 'mfn-opts'),
					'description' 	=> __('Portfolio items order.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'DESC' 	=> 'Descending',
						'ASC' 	=> 'Ascending',
					)),
				),

			)
		));

		// Portfolio Photo

		vc_map(array(
			'base' 			=> 'portfolio_photo',
			'name' 			=> __('Portfolio Photo', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-portfolio_photo',
			'params' 		=> array(

				array(
					'param_name' 	=> 'count',
					'type' 			=> 'textfield',
					'heading' 		=> __('Count', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> 4,
				),

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'textfield',
					'heading' 		=> __('Category', 'mfn-opts'),
					'description' 	=> __('Portfolio Category slug', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'category_multi',
					'type' 			=> 'textfield',
					'heading' 		=> __('Multiple Categories', 'mfn-opts'),
					'description' 	=> __('Slugs should be separated with <strong>coma</strong> (,)', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'orderby',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order by', 'mfn-opts'),
					'description' 	=> __('Portfolio items order by column.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'date'			=> 'Date',
						'menu_order' 	=> 'Menu order',
						'title'			=> 'Title',
						'rand'			=> 'Random',
					)),
				),

				array(
					'param_name' 	=> 'order',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order', 'mfn-opts'),
					'description' 	=> __('Portfolio items order.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'DESC' 	=> 'Descending',
						'ASC' 	=> 'Ascending',
					)),
				),

			)
		));

		// Portfolio Slider

		vc_map(array(
			'base' 			=> 'portfolio_slider',
			'name' 			=> __('Portfolio Slider', 'mfn-opts'),
			'description' 	=> __('Item do NOT work in Frontend Editor', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-portfolio_slider',
			'params' 		=> array(

				array(
					'param_name' 	=> 'count',
					'type' 			=> 'textfield',
					'heading' 		=> __('Count', 'mfn-opts'),
					'admin_label'	=> true,
					'value'			=> 6,
				),

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'textfield',
					'heading' 		=> __('Category', 'mfn-opts'),
					'description' 	=> __('Portfolio Category slug', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'category_multi',
					'type' 			=> 'textfield',
					'heading' 		=> __('Multiple Categories', 'mfn-opts'),
					'description' 	=> __('Slugs should be separated with <strong>coma</strong> (,)', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'orderby',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order by', 'mfn-opts'),
					'description' 	=> __('Portfolio items order by column.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'date'			=> 'Date',
						'menu_order' 	=> 'Menu order',
						'title'			=> 'Title',
						'rand'			=> 'Random',
					)),
				),

				array(
					'param_name' 	=> 'order',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order', 'mfn-opts'),
					'description' 	=> __('Portfolio items order.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'DESC' 	=> 'Descending',
						'ASC' 	=> 'Ascending',
					)),
				),

				array(
					'param_name' 	=> 'arrows',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Navigation Arrows', 'mfn-opts'),
					'description' 	=> __('Show Navigation Arrows', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						''			=> 'None',
						'hover' 	=> 'Show on hover',
						'always' 	=> 'Always show',
					)),
				),

			)
		));

		// Pricing Item

		vc_map(array(
			'base' 			=> 'pricing_item',
			'name' 			=> __('Pricing Item', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-pricing_item',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'price',
					'type' 			=> 'textfield',
					'heading' 		=> __('Price', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'currency',
					'type' 			=> 'textfield',
					'heading' 		=> __('Currency', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'currency_pos',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Currency | Position', 'mfn-opts'),
					'value' 		=> array_flip(array(
						'' 			=> 'Left',
						'right'		=> 'Right'
					)),
				),

				array(
					'param_name' 	=> 'period',
					'type' 			=> 'textfield',
					'heading' 		=> __('Period', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'subtitle',
					'type' 			=> 'textfield',
					'heading' 		=> __('Subtitle', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'description' 	=> __('HTML tags allowed', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> '<ul><li><strong>List</strong> item</li></ul>',
				),

				array(
					'param_name' 	=> 'link_title',
					'type' 			=> 'textfield',
					'heading' 		=>  __('Button | Title', 'mfn-opts'),
					'description'	=> __('Link will appear only if this field will be filled.', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'icon',
					'type' 			=> 'textfield',
					'heading' 		=> __('Button | Icon', 'mfn-opts'),
					'description' 	=> __('Font Icon, eg. <strong>icon-lamp</strong>', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Button | Link', 'mfn-opts'),
					'description'	=> __('Link will appear only if this field will be filled.', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Button | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

				array(
					'param_name' 	=> 'featured',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Featured', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'box'	=> 'Box',
						'label'	=> 'Table Label',
						'table'	=> 'Table',
					)),
				),

			)
		));

		// Progress Bars

		vc_map(array(
			'base' 			=> 'progress_bars',
			'name' 			=> __('Progress Bars', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-progress_bars',
			'params' 		=> array(

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'description'	=> __('Please use <strong>[bar title="Title" value="50" size="20"]</strong> shortcodes here.', 'mfn-opts'),
					'admin_label'	=> false,
					'value' 		=> '[bar title="Bar1" value="50" size="20"]'."\n".'[bar title="Bar2" value="60" size="20"]',
				),

			)
		));

		// Promo Box

		vc_map(array(
			'base' 			=> 'promo_box',
			'name' 			=> __('Promo Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-promo_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'btn_text',
					'type' 			=> 'textfield',
					'heading' 		=> __('Button | Text', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'btn_link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Button | Link', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Button | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

				array(
					'param_name' 	=> 'position',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Image position', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'left' 	=> 'Left',
						'right' => 'Right',
					)),
				),

				array(
					'param_name' 	=> 'border',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Border', 'mfn-opts'),
					'admin_label'	=> false,
					'description'	=> __('Show right border', 'mfn-opts'),
					'value' 		=> array(
						__('No', 'mfn-opts') 	=> 0,
						__('Yes', 'mfn-opts')	=> 1,
					),
				),

			)
		));

		// Quick Fact

		vc_map(array(
			'base' 			=> 'quick_fact',
			'name' 			=> __('Quick Fact', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-quick_fact',
			'params' 		=> array(

				array(
					'param_name' 	=> 'heading',
					'type' 			=> 'textfield',
					'heading' 		=> __('Heading', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'number',
					'type' 			=> 'textfield',
					'heading' 		=> __('Number', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'prefix',
					'type' 			=> 'textfield',
					'heading' 		=> __('Prefix', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'label',
					'type' 			=> 'textfield',
					'heading' 		=> __('Postfix', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> false,
				),

			)
		));

		// Slider

		vc_map(array(
			'base' 			=> 'slider',
			'name' 			=> __('Slider', 'mfn-opts'),
			'description' 	=> __('Item do NOT work in Frontend Editor', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-slider',
			'params' 		=> array(

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						''				=> 'Default',
						'description'	=> 'Description',
						'flat' 			=> 'Flat',
						'carousel' 		=> 'Carousel',
					)),
				),

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'textfield',
					'heading' 		=> __('Category', 'mfn-opts'),
					'description' 	=> __('Category slug', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'orderby',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order by', 'mfn-opts'),
					'description' 	=> __('Portfolio items order by column.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'date'			=> 'Date',
						'menu_order' 	=> 'Menu order',
						'title'			=> 'Title',
						'rand'			=> 'Random',
					)),
				),

				array(
					'param_name' 	=> 'order',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order', 'mfn-opts'),
					'description' 	=> __('Portfolio items order.', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'DESC' 	=> 'Descending',
						'ASC' 	=> 'Ascending',
					)),
				),

			)
		));

		// Sliding Box

		vc_map(array(
			'base' 			=> 'sliding_box',
			'name' 			=> __('Sliding Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-sliding_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Story Box

		vc_map(array(
			'base' 			=> 'story_box',
			'name' 			=> __('Story Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-story_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						''			=> 'Horizontal Image',
						'vertical' 	=> 'Vertical Image',
					)),
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Testimonials

		vc_map(array(
			'base' 			=> 'testimonials',
			'name' 			=> __('Testimonials', 'mfn-opts'),
			'description' 	=> __('Item do NOT work in Frontend Editor', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-testimonials',
			'params' 		=> array(

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'textfield',
					'heading' 		=> __('Category', 'mfn-opts'),
					'description' 	=> __('Category slug', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'orderby',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order by', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'menu_order' 	=> 'Menu order',
						'date'			=> 'Date',
						'title'			=> 'Title',
						'rand'			=> 'Random',
					)),
				),

				array(
					'param_name' 	=> 'order',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'ASC' 	=> 'Ascending',
						'DESC' 	=> 'Descending',
					)),
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'' 				=> __('Default', 'mfn-opts'),
						'single-photo' 	=> __('Single Photo', 'mfn-opts'),
					)),
				),

				array(
					'param_name' 	=> 'hide_photos',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Hide Photos', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						0 => __('No', 'mfn-opts'),
						1 => __('Yes', 'mfn-opts'),
					)),
				),

			)
		));

		// Testimonials List

		vc_map(array(
			'base' 			=> 'testimonials_list',
			'name' 			=> __('Testimonials List', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-testimonials_list',
			'params' 		=> array(

				array(
					'param_name' 	=> 'category',
					'type' 			=> 'textfield',
					'heading' 		=> __('Category', 'mfn-opts'),
					'description' 	=> __('Category slug', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'orderby',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order by', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'menu_order' 	=> 'Menu order',
						'date'			=> 'Date',
						'title'			=> 'Title',
						'rand'			=> 'Random',
					)),
				),

				array(
					'param_name' 	=> 'order',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Order', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'ASC' 	=> 'Ascending',
						'DESC' 	=> 'Descending',
					)),
				),

				array(
					'param_name' 	=> 'style',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Style', 'mfn-opts'),
					'admin_label'	=> true,
					'value' 		=> array_flip(array(
						'' 			=> __('Default', 'mfn-opts'),
						'quote' 	=> __('Quote above the author', 'mfn-opts'),
					)),
				),

			)
		));

		// Trailer Box

		vc_map(array(
			'base' 			=> 'trailer_box',
			'name' 			=> __('Trailer Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-trailer_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'slogan',
					'type' 			=> 'textfield',
					'heading' 		=> __('Slogan', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'title',
					'type' 			=> 'textfield',
					'heading' 		=> __('Title', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));

		// Zoom Box

		vc_map(array(
			'base' 			=> 'zoom_box',
			'name' 			=> __('Zoom Box', 'mfn-opts'),
			'category' 		=> __('Muffin Builder', 'mfn-opts'),
			'icon' 			=> 'mfn-vc-icon-zoom_box',
			'params' 		=> array(

				array(
					'param_name' 	=> 'image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'bg_color',
					'type' 			=> 'colorpicker',
					'heading' 		=> __('Overlay background', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'content_image',
					'type' 			=> 'attach_image',
					'heading' 		=> __('Content Image', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'content',
					'type' 			=> 'textarea',
					'heading' 		=> __('Content', 'mfn-opts'),
					'admin_label'	=> false,
				),

				array(
					'param_name' 	=> 'link',
					'type' 			=> 'textfield',
					'heading' 		=> __('Link', 'mfn-opts'),
					'admin_label'	=> true,
				),

				array(
					'param_name' 	=> 'target',
					'type' 			=> 'dropdown',
					'heading' 		=> __('Link | Target', 'mfn-opts'),
					'admin_label'	=> false,
					'value'			=> array_flip(array(
						'' 			=> 'Default | _self',
						'_blank' 	=> 'New Tab or Window | _blank' ,
						'lightbox' 	=> 'Lightbox (image or embed video)',
					)),
				),

			)
		));
	}
}
