<?php

namespace WBCR\FactoryClearfy227\Pages;

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Класс страницы, которая реализует функции мастера установки.
 *
 * Этот класс унаследован от стандартного шаблона страницы \Wbcr_FactoryPages435_ImpressiveThemplate,
 * поэтому все его инструменты могут быть применены и в этом классе. Но вы должны учитывать, что
 * поведение экшенов страницы было изменено. В данной реализации экшены используется для пагинации шагов.
 *
 * @package WBCR\FactoryClearfy227\Pages
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @since         2.2.2
 */
class Setup extends \Wbcr_FactoryPages435_ImpressiveThemplate {

	const DEFAULT_STEP = 'step0';

	/**
	 * @var string
	 */
	public $type = 'page';

	/**
	 * {@inheritDoc}
	 *
	 * @since   2.2.2 - добавлен
	 * @var bool
	 */
	public $page_parent_page = 'none';

	/**
	 * @var string
	 */
	public $menu_target = 'options-general.php';

	/**
	 * {@inheritDoc}
	 *
	 * @since   2.2.2 - добавлен
	 * @var bool
	 */
	public $show_right_sidebar_in_options = false;

	/**
	 * {@inheritDoc}
	 *
	 * @since  2.2.2 - добавлен
	 * @var bool
	 */
	public $available_for_multisite = false;

	/**
	 * {@inheritDoc}
	 *
	 * @since   2.2.2 - добавлен
	 * @var bool
	 */
	public $internal = true;

	private $current_step = 'step0';
	private $steps = [];

	/**
	 * @param \Wbcr_Factory436_Plugin $plugin
	 */
	public function __construct(\Wbcr_Factory436_Plugin $plugin)
	{
		$this->id = 'setup';

		$this->menu_title = __('Setup master', 'wbcr_factory_clearfy_227');
		$this->page_menu_short_description = __('Setup master', 'wbcr_factory_clearfy_227');
		parent::__construct($plugin);
	}

	public function getPageTitle()
	{
		return __('Setup', 'wbcr_factory_clearfy_227');
	}

	public function get_close_wizard_url()
	{
		return $this->plugin->getPluginPageUrl('quick_start');
	}

	/**
	 * Поведение экшенов страницы было изменено. В данной реализации экшены используется для пагинации шагов.
	 *
	 * @param string $action
	 *
	 * @throws \Exception
	 */
	public function executeByName($action)
	{
		$step = self::DEFAULT_STEP;

		if( false !== strpos($action, 'step') && isset($this->steps[$action]) ) {
			$step = $this->current_step = $action;
		}

		ob_start();
		$this->steps[$step]->html();
		$step_content = ob_get_clean();

		$this->showPage($step_content);
	}

	/**
	 * Регистрируем класс обработчик шага
	 *
	 * Класс обработчик полностью отвечает за шаблон и функционально шага.
	 *
	 * @param string $path
	 * @param string $class_name
	 * @throws \Exception
	 */
	protected function register_step($path, $class_name)
	{
		require_once $path;

		if( !class_exists($class_name) ) {
			throw new \Exception("Class {$class_name} is not found!");
		}

		$step = new $class_name($this);
		$this->steps[$step->get_id()] = $step;
	}

	/**
	 * Requests assets (js and css) for the page.
	 *
	 * @param \Wbcr_Factory436_ScriptList $scripts
	 * @param \Wbcr_Factory436_StyleList $styles
	 *
	 * @return void
	 * @see Wbcr_FactoryPages435_AdminPage
	 *
	 */
	public function assets($scripts, $styles)
	{
		parent::assets($scripts, $styles);

		$this->styles->add(FACTORY_CLEARFY_227_URL . '/assets/css/page-setup.css');

		// Require step assets
		if( isset($_GET['action']) && false !== strpos($_GET['action'], 'step') && isset($this->steps[$_GET['action']]) ) {
			$this->steps[$_GET['action']]->assets($scripts, $styles);
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $content
	 * @since   2.2.2 - добавлен
	 */
	protected function showPage($content = null)
	{
		?>
		<div class="w-factory-clearfy-227-setup">
			<ol class="w-factory-clearfy-227-setup-steps">
				<?php foreach($this->steps as $step): ?>
					<?php if( self::DEFAULT_STEP === $step->get_id() ) {
						continue;
					} ?>
					<li <?php if($this->current_step === $step->get_id()): ?>class="active"<?php endif; ?>><?php echo $step->get_title(); ?></li>
				<?php endforeach; ?>
			</ol>
			<div class="w-factory-clearfy-227-setup-content">
				<?php echo $content; ?>
			</div>
			<a class="w-factory-clearfy-227-setup-footer-links" href="<?php echo esc_url($this->get_close_wizard_url()); ?>">
				<?php _e('Not now', 'wbcr_factory_clearfy_227') ?>
			</a>
		</div>
		<?php
	}
}
