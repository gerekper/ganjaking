<?php

namespace Premmerce\PrimaryCategory\Admin;

use Premmerce\PrimaryCategory\Model\Model;
use Premmerce\PrimaryCategory\PrimaryCategory;
use \WP_Post;

/**
 * Class Admin
 * @package Premmerce\PrimaryCategory\Admin
 *
 * This class is responsible for admin site part functionality
 */
class Admin
{
	const PRIMARY_CATEGORY_INPUT_NAME = 'premmerce-primary-category-id';

	/**
	 * @var string
	 */
	private $mainFilePath;
	/**
	 * @var Model
	 */
	private $model;


	/**
	 * Admin constructor.
	 *
	 * @param Model $model
	 */
	public function __construct(Model $model)
	{
		$this->mainFilePath = PrimaryCategory::$mainFilePath;
		$this->model = $model;

		if($this->isPostEditPage()){
			$this->setHooks();
		}
	}

	/**
	 *
	 */
	public function setHooks(){
		add_action('admin_enqueue_scripts', [$this, 'enqueueAssets'] );
		add_action('edit_form_advanced', [$this, 'outputPrimaryCategoryField']);
		add_action('post_updated', [$this, 'savePostPrimaryCategory']);
	}

	/**
	 * @return bool
	 */
	private function isPostEditPage()
	{
		$isPostsPage = false;

		if( function_exists('get_current_screen')){
			if(isset(get_current_screen()->base) && get_current_screen()->base === 'post')
			{
				$isPostsPage = true;
			}
		}

		return $isPostsPage;
	}

	/**
	 * @param WP_Post $post
	 */
	public function outputPrimaryCategoryField(WP_Post $post)
	{
		$primaryCategoryId = $this->model->getPrimaryCategoryId($post->ID);
		echo $this->renderPrimaryCategoryIdField($primaryCategoryId);
	}

	/**
	 * @param int|null $primaryCategoryId
	 *
	 * @return string
	 */
	private function renderPrimaryCategoryIdField($primaryCategoryId)
	{
		return sprintf('<input type=hidden id="%1$s" name="%1$s" value="%2$s">', self::PRIMARY_CATEGORY_INPUT_NAME, $primaryCategoryId);
	}

	/**
	 *
	 */
	public function enqueueAssets()
	{
		$assetsHandle = 'premmerce-primary-category';

		global $post;

		wp_enqueue_style(
			$assetsHandle,
			$this->getFileUrl('vendor/premmerce/primary-category/assets/admin/css/primary-category.css'),
			[],
			PrimaryCategory::VERSION
		);

		wp_enqueue_script(
			$assetsHandle,
			$this->getFileUrl('vendor/premmerce/primary-category/assets/admin/js/primary-category.js'),
			['jquery'],
			PrimaryCategory::VERSION,
			true
		);

		wp_localize_script(
			$assetsHandle,
			'premmerceSettings',
			[
				'categoryIdFieldName' => self::PRIMARY_CATEGORY_INPUT_NAME,
				'mainCategoryId' => $this->model->getPrimaryCategoryId($post->ID),
				'makePrimaryText' => __('Make primary', 'premmerce-primary-category'),
				'primarySpanText' => __('Primary', 'premmerce-primary-category')
			]
		);
	}

	/**
	 * @param string $relativePath Relative path starting from plugin root
	 *
	 * @return string
	 */
	private function getFileUrl($relativePath)
	{
		$pluginDirUrl = plugin_dir_url($this->mainFilePath);

		return $pluginDirUrl . $relativePath;
	}

	/**
	 * @param int $postId
	 */
	public function savePostPrimaryCategory($postId){
		$primaryCategoryId = filter_input(INPUT_POST, self::PRIMARY_CATEGORY_INPUT_NAME, FILTER_SANITIZE_NUMBER_INT);
		$this->model->updatePrimaryCategory($postId, $primaryCategoryId);
	}
}
