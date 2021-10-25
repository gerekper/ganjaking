<?php

namespace MasterAddons\Modules\DynamicTags;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

class Extension_Dynamic_Tags
{
	private static $_instance = null;

	public function __construct()
	{
		add_action('elementor/dynamic_tags/register_tags', [$this, 'jltma_register_dynamic_tags']);
	}

	/**
	 * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags
	 */
	public function jltma_register_dynamic_tags($dynamic_tags)
	{

		$tags = array(
			'jltma-archive-description' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'archive-description.php',
				'class' => 'Tags\JLTMA_Archive_Description',
				'group' => 'archive',
				'title' => 'Archive',
			),
			'jltma-archive-meta' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'archive-meta.php',
				'class' => 'Tags\JLTMA_Archive_Meta',
				'group' => 'archive',
				'title' => 'Archive',
			),
			'jltma-archive-title' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'archive-title.php',
				'class' => 'Tags\JLTMA_Archive_Title',
				'group' => 'archive',
				'title' => 'Archive',
			),
			'jltma-archive-url' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'archive-url.php',
				'class' => 'Tags\JLTMA_Archive_URL',
				'group' => 'archive',
				'title' => 'Archive',
			),
			'jltma-author-info' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'author-info.php',
				'class' => 'Tags\JLTMA_Author_Info',
				'group' => 'author',
				'title' => 'Author',
			),
			'jltma-author-meta' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'author-meta.php',
				'class' => 'Tags\JLTMA_Author_Meta',
				'group' => 'author',
				'title' => 'Author',
			),
			'jltma-author-name' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'author-name.php',
				'class' => 'Tags\JLTMA_Author_Name',
				'group' => 'author',
				'title' => 'Author',
			),
			'jltma-author-profile-picture' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'author-profile-picture.php',
				'class' => 'Tags\JLTMA_Author_Profile_Picture',
				'group' => 'author',
				'title' => 'Author',
			),
			'jltma-author-url' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'author-url.php',
				'class' => 'Tags\JLTMA_Author_URL',
				'group' => 'author',
				'title' => 'Author',
			),
			'jltma-comments-number' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'comments-number.php',
				'class' => 'Tags\JLTMA_Comments_Number',
				'group' => 'comments',
				'title' => 'Comments',
			),
			'jltma-comments-url' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'comments-url.php',
				'class' => 'Tags\JLTMA_Comments_URL',
				'group' => 'comments',
				'title' => 'Comments',
			),
			'jltma-contact-url' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'contact-url.php',
				'class' => 'Tags\JLTMA_Contact_URL',
				'group' => 'action',
				'title' => 'Action',
			),
			'jltma-current-date-time' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'current-date-time.php',
				'class' => 'Tags\JLTMA_Current_Date_Time',
				'group' => 'site',
				'title' => 'Site',
			),
			'jltma-featured-image-data' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'featured-image-data.php',
				'class' => 'Tags\JLTMA_Featured_Image_Data',
				'group' => 'media',
				'title' => 'Media',
			),
			'jltma-page-title' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'page-title.php',
				'class' => 'Tags\JLTMA_Page_Title',
				'group' => 'site',
				'title' => 'Site',
			),
			'jltma-post-custom-field' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-custom-field.php',
				'class' => 'Tags\Post_Custom_Field',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-pages-url' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'pages-url.php',
				'class' => 'Tags\JLTMA_Pages_Url',
				'group' => 'URL',
				'title' => 'URL',
			),
			'jltma-cats-url' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'taxonomies-url.php',
				'class' => 'Tags\JLTMA_Taxonomies_Url',
				'group' => 'URL',
				'title' => 'URL',
			),
			'jltma-post-date' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-date.php',
				'class' => 'Tags\JLTMA_Post_Date',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-post-excerpt' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-excerpt.php',
				'class' => 'Tags\JLTMA_Post_Excerpt',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-post-featured-image' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-featured-image.php',
				'class' => 'Tags\JLTMA_Post_Featured_Image',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-post-gallery' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-gallery.php',
				'class' => 'Tags\JLTMA_Post_Gallery',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-post-id' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-id.php',
				'class' => 'Tags\JLTMA_Post_ID',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-post-terms' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-terms.php',
				'class' => 'Tags\JLTMA_Post_Terms',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-post-time' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-time.php',
				'class' => 'Tags\JLTMA_Post_Time',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-post-title' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-title.php',
				'class' => 'Tags\JLTMA_Post_Title',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-post-url' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'post-url.php',
				'class' => 'Tags\JLTMA_Post_URL',
				'group' => 'post',
				'title' => 'Post',
			),
			'jltma-request-parameter' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'request-parameter.php',
				'class' => 'Tags\JLTMA_Request_Parameter',
				'group' => 'site',
				'title' => 'Site',
			),
			'jltma-shortcode' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'shortcode.php',
				'class' => 'Tags\JLTMA_Shortcode',
				'group' => 'site',
				'title' => 'Site',
			),
			'jltma-site-logo' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'site-logo.php',
				'class' => 'Tags\JLTMA_Site_Logo',
				'group' => 'site',
				'title' => 'Site',
			),
			'jltma-site-tagline' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'site-tagline.php',
				'class' => 'Tags\JLTMA_Site_Tagline',
				'group' => 'site',
				'title' => 'Site',
			),
			'jltma-site-title' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'site-title.php',
				'class' => 'Tags\JLTMA_Site_Title',
				'group' => 'site',
				'title' => 'Site',
			),
			'jltma-site-url' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'site-url.php',
				'class' => 'Tags\Site_URL',
				'group' => 'site',
				'title' => 'Site',
			),
			'jltma-user-info' => array(
				'file'  => JLTMA_DYNAMIC_TAGS_PATH_INC . 'user-info.php',
				'class' => 'Tags\JLTMA_User_Info',
				'group' => 'site',
				'title' => 'Site',
			)
		);

		foreach ($tags as $tags_type => $tags_info) {
			if (!empty($tags_info['file']) && !empty($tags_info['class'])) {
				// In our Dynamic Tag we use a group named request-variables so we need
				// To register that group as well before the tag
				Master_Addons_Helper::jltma_elementor()->dynamic_tags->register_group($tags_info['group'], [
					'title' => $tags_info['title']
				]);

				include_once($tags_info['file']);

				if (class_exists($tags_info['class'])) {
					$class_name = $tags_info['class'];
				} elseif (class_exists(__NAMESPACE__ . '\\' . $tags_info['class'])) {
					$class_name = __NAMESPACE__ . '\\' . $tags_info['class'];
				}

				$dynamic_tags->register_tag($class_name);
			}
		}
	}


	public static function get_instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

Extension_Dynamic_Tags::get_instance();
