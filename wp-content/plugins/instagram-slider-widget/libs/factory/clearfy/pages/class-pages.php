<?php

/**
 * Общий класс прослойка для страниц Clearfy и его компоннетов.
 * В этом классе добавляются общие ресурсы и элементы, необходимые для всех связанных плагинов.
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @since         2.0.5
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Class Wbcr_FactoryPages435_ImpressiveThemplate
 *
 * @method string getInfoWidget() - get widget content information
 * @method string getRatingWidget(array $args = []) - get widget content rating
 * @method string getDonateWidget() - get widget content donate
 * @method string getBusinessSuggetionWidget()
 * @method string getSupportWidget
 */
class Wbcr_FactoryClearfy227_PageBase extends Wbcr_FactoryPages435_ImpressiveThemplate {

	/**
	 * {@inheritDoc}
	 *
	 * @since   2.0.5 - добавлен
	 * @var bool
	 */
	public $show_right_sidebar_in_options = true;

	/**
	 * {@inheritDoc}
	 *
	 * @since  2.0.5 - добавлен
	 * @var bool
	 */
	public $available_for_multisite = true;

	/**
	 * {@inheritDoc}
	 *
	 * @since  2.0.6 - добавлен
	 * @var bool
	 */
	public $internal = true;

	/**
	 * Show on the page a search form for search options of plugin?
	 *
	 * @since  2.2.0 - Added
	 * @var bool - true show, false hide
	 */
	public $show_search_options_form = true;

	/**
	 * @param Wbcr_Factory436_Plugin $plugin
	 */
	public function __construct(Wbcr_Factory436_Plugin $plugin)
	{
		parent::__construct($plugin);

		if( $this->show_search_options_form && "options" === $this->type && "hide_my_wp" !== $this->id ) {
			$this->register_options_to_search();
		}
	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return null|string
	 */
	public function __call($name, $arguments)
	{
		if( substr($name, 0, 3) == 'get' ) {
			$called_method_name = 'show' . substr($name, 3);
			if( method_exists($this, $called_method_name) ) {
				ob_start();

				$this->$called_method_name($arguments);
				$content = ob_get_contents();
				ob_end_clean();

				return $content;
			}
		}

		return null;
	}

	/**
	 * Requests assets (js and css) for the page.
	 *
	 * @param Wbcr_Factory436_ScriptList $scripts
	 * @param Wbcr_Factory436_StyleList $styles
	 *
	 * @return void
	 * @see Wbcr_FactoryPages435_AdminPage
	 *
	 */
	public function assets($scripts, $styles)
	{
		parent::assets($scripts, $styles);

		$this->styles->add(FACTORY_CLEARFY_227_URL . '/assets/css/clearfy-base.css');

		// todo: вынести все общие скрипты и стили фреймворка, продумать совместимость с другими плагинами
		if( defined('WCL_PLUGIN_URL') ) {
			$this->styles->add(WCL_PLUGIN_URL . '/admin/assets/css/general.css');
		}

		// Script for search form on plugin options
		if( $this->show_search_options_form && "options" === $this->type ) {
			$this->styles->add(FACTORY_CLEARFY_227_URL . '/assets/css/libs/autocomplete.css');

			$this->scripts->add(FACTORY_CLEARFY_227_URL . '/assets/js/libs/jquery.autocomplete.min.js');
			$this->scripts->add(FACTORY_CLEARFY_227_URL . '/assets/js/clearfy-search-options.js');
		}

		/**
		 * Allows you to enqueue scripts to the internal pages of the plugin.
		 * $this->getResultId() - page id + plugin name = quick_start-wbcr_clearfy
		 *
		 * @since 2.0.5
		 */
		do_action('wbcr/clearfy/page_assets', $this->getResultId(), $scripts, $styles);
	}

	/**
	 * @return Wbcr_Factory436_Request
	 */
	public function request()
	{
		return $this->plugin->request;
	}

	/**
	 * @param      $option_name
	 * @param bool $default *
	 *
	 * @return mixed|void
	 * @since 2.0.5
	 *
	 */
	public function getPopulateOption($option_name, $default = false)
	{
		return $this->plugin->getPopulateOption($option_name, $default);
	}

	/**
	 * @param      $option_name
	 * @param bool $default
	 *
	 * @return mixed|void
	 */
	public function getOption($option_name, $default = false)
	{
		return $this->plugin->getOption($option_name, $default);
	}

	/**
	 * @param $option_name
	 * @param $value
	 *
	 * @return void
	 */
	public function updatePopulateOption($option_name, $value)
	{
		$this->plugin->updatePopulateOption($option_name, $value);
	}

	/**
	 * @param $option_name
	 * @param $value
	 *
	 * @return void
	 */
	public function updateOption($option_name, $value)
	{
		$this->plugin->updateOption($option_name, $value);
	}

	/**
	 * @param $option_name
	 *
	 * @return void
	 */
	public function deletePopulateOption($option_name)
	{
		$this->plugin->deletePopulateOption($option_name);
	}

	/**
	 * @param $option_name
	 *
	 * @return void
	 */
	public function deleteOption($option_name)
	{
		$this->plugin->deleteOption($option_name);
	}

	/**
	 * @param string $position
	 *
	 * @return mixed|void
	 */
	protected function getPageWidgets($position = 'bottom')
	{
		$widgets = [];

		if( $position == 'bottom' ) {
			$widgets['info_widget'] = $this->getInfoWidget();
			$widgets['rating_widget'] = $this->getRatingWidget();
			$widgets['support_widget'] = $this->getSupportWidget();
			//$widgets['donate_widget'] = $this->getDonateWidget();
		} else if( $position == 'right' ) {
			$widgets['business_suggetion'] = $this->getBusinessSuggetionWidget();
			$widgets['info_widget'] = $this->getInfoWidget();
			$widgets['rating_widget'] = $this->getRatingWidget();
		}

		/**
		 * @since 4.0.9 - является устаревшим
		 */
		$widgets = wbcr_factory_436_apply_filters_deprecated('wbcr_factory_pages_435_imppage_get_widgets', [
			$widgets,
			$position,
			$this->plugin,
			$this
		], '4.0.9', 'wbcr/factory/pages/impressive/widgets');

		/**
		 * @since 4.0.1 - добавлен
		 * @since 4.0.9 - изменено имя
		 */
		$widgets = apply_filters('wbcr/factory/pages/impressive/widgets', $widgets, $position, $this->plugin, $this);

		return $widgets;
	}

	/**
	 * Создает Html разметку виджета для рекламы премиум версии
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.0.2
	 */
	public function showBusinessSuggetionWidget()
	{
		$plugin_name = $this->plugin->getPluginName();
		$upgrade_price = $this->plugin->has_premium() ? $this->plugin->premium->get_price() : 0;
		$purchase_url = $this->plugin->get_support()->get_pricing_url(true, 'right_sidebar_ads');

		$default_features = [
			'4_premium' => __('4 premium components now;', 'wbcr_factory_clearfy_227'),
			'40_premium' => __('40 new premium components within a year for the single price;', 'wbcr_factory_clearfy_227'),
			'multisite_support' => __('Multisite support;', 'wbcr_factory_clearfy_227'),
			'advance_settings' => __('Advanced settings;', 'wbcr_factory_clearfy_227'),
			'no_ads' => __('No ads;', 'wbcr_factory_clearfy_227'),
			'perfect_support' => __('Perfect support.', 'wbcr_factory_clearfy_227')
		];

		/**
		 * @since 2.0.8 - added
		 */
		$suggetion_title = __('MORE IN CLEARFY <span>BUSINESS</span>', 'wbcr_factory_clearfy_227');
		$suggetion_title = apply_filters('wbcr/clearfy/pages/suggetion_title', $suggetion_title, $plugin_name, $this->id);

		/**
		 * @since 2.0.8 - deprecated
		 */
		$suggetion_features = wbcr_factory_436_apply_filters_deprecated('wbcr/clearfy/page_bussines_suggetion_features', [
			$default_features,
			$this->plugin->getPluginName(),
			$this->id
		], '2.0.8', 'wbcr/clearfy/pages/suggetion_features');

		/**
		 * @since 2.0.8 - renamed
		 * @since 2.0.6
		 */
		$suggetion_features = apply_filters('wbcr/clearfy/pages/suggetion_features', $suggetion_features, $plugin_name, $this->id);

		if( empty($suggetion_features) ) {
			$suggetion_features = $default_features;
		}
		?>
		<div class="wbcr-factory-sidebar-widget wbcr-factory-clearfy-227-pro-suggettion">
			<h3><?php echo $suggetion_title; ?></h3>
			<ul>
				<?php if( !empty($suggetion_features) ): ?>
					<?php foreach($suggetion_features as $feature): ?>
						<li><?= $feature ?></li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
			<a href="<?php echo $purchase_url ?>" class="wbcr-factory-purchase-premium" target="_blank"
			   rel="noopener">
				<?php printf(__('Upgrade for $%s', 'wbcr_factory_clearfy_227'), $upgrade_price) ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Создает html разметку виджета с информационными маркерами
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.0.0
	 */
	public function showInfoWidget()
	{
		?>
		<div class="wbcr-factory-sidebar-widget">
			<ul>
				<li>
						<span class="wbcr-factory-hint-icon-simple wbcr-factory-simple-red">
							<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAQAAABKmM6bAAAAUUlEQVQIHU3BsQ1AQABA0X/komIrnQHYwyhqQ1hBo9KZRKL9CBfeAwy2ri42JA4mPQ9rJ6OVt0BisFM3Po7qbEliru7m/FkY+TN64ZVxEzh4ndrMN7+Z+jXCAAAAAElFTkSuQmCC"
							     alt=""/>
						</span>
					- <?php _e('A neutral setting that can not harm your site, but you must be sure that you need to use it.', 'wbcr_factory_clearfy_227'); ?>
				</li>
				<li>
						<span class="wbcr-factory-hint-icon-simple wbcr-factory-simple-grey">
							<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAQAAABKmM6bAAAAUUlEQVQIHU3BsQ1AQABA0X/komIrnQHYwyhqQ1hBo9KZRKL9CBfeAwy2ri42JA4mPQ9rJ6OVt0BisFM3Po7qbEliru7m/FkY+TN64ZVxEzh4ndrMN7+Z+jXCAAAAAElFTkSuQmCC"
							     alt=""/>
						</span>
					- <?php _e('When set this option, you must be careful. Plugins and themes may depend on this function. You must be sure that you can disable this feature for the site.', 'wbcr_factory_clearfy_227'); ?>
				</li>
				<li>
						<span class="wbcr-factory-hint-icon-simple wbcr-factory-simple-green">
							<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAQAAABKmM6bAAAAUUlEQVQIHU3BsQ1AQABA0X/komIrnQHYwyhqQ1hBo9KZRKL9CBfeAwy2ri42JA4mPQ9rJ6OVt0BisFM3Po7qbEliru7m/FkY+TN64ZVxEzh4ndrMN7+Z+jXCAAAAAElFTkSuQmCC"
							     alt=""/>
						</span>
					- <?php _e('Absolutely safe setting, We recommend to use.', 'wbcr_factory_clearfy_227'); ?>
				</li>
			</ul>
			----------<br>
			<p><?php _e('Hover to the icon to get help for the feature you selected.', 'wbcr_factory_clearfy_227'); ?></p>
		</div>
		<?php
	}

	/**
	 * Создает html разметку виджета рейтинга
	 *
	 * @param array $args
	 *
	 * @since  2.0.0
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function showRatingWidget(array $args)
	{
		if( !isset($args[0]) || empty($args[0]) ) {
			$page_url = "https://goo.gl/tETE2X";
		} else {
			$page_url = $args[0];
		}

		$page_url = apply_filters('wbcr_factory_pages_435_imppage_rating_widget_url', $page_url, $this->plugin->getPluginName(), $this->getResultId());

		?>
		<div class="wbcr-factory-sidebar-widget">
			<p>
				<strong><?php _e('Do you want the plugin to improved and update?', 'wbcr_factory_clearfy_227'); ?></strong>
			</p>
			<p><?php _e('Help the author, leave a review on wordpress.org. Thanks to feedback, I will know that the plugin is really useful to you and is needed.', 'wbcr_factory_clearfy_227'); ?></p>
			<p><?php _e('And also write your ideas on how to extend or improve the plugin.', 'wbcr_factory_clearfy_227'); ?></p>
			<p>
				<i class="wbcr-factory-icon-5stars"></i>
				<a href="<?= $page_url ?>" title="Go rate us" target="_blank">
					<strong><?php _e('Go rate us and push ideas', 'wbcr_factory_clearfy_227'); ?></strong>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Создает html размету виджета доната
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.0.0
	 */
	public function showDonateWidget()
	{
		?>
		<div class="wbcr-factory-sidebar-widget">
			<p>
				<strong><?php _e('Donation for plugin development', 'wbcr_factory_clearfy_227'); ?></strong>
			</p>
			<?php if( get_locale() !== 'ru_RU' ): ?>
				<form id="wbcr-factory-paypal-donation-form" action="https://www.paypal.com/cgi-bin/webscr"
				      method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="VDX7JNTQPNPFW">
					<div class="wbcr-factory-donation-price">5$</div>
					<input type="image" src="<?= FACTORY_PAGES_435_URL ?>/templates/assets/img/paypal-donate.png"
					       border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
				</form>
			<?php else: ?>
				<iframe frameborder="0" allowtransparency="true" scrolling="no"
				        src="https://money.yandex.ru/embed/donate.xml?account=410011242846510&quickpay=donate&payment-type-choice=on&mobile-payment-type-choice=on&default-sum=300&targets=%D0%9D%D0%B0+%D0%BF%D0%BE%D0%B4%D0%B4%D0%B5%D1%80%D0%B6%D0%BA%D1%83+%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD%D0%B0+%D0%B8+%D1%80%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D1%83+%D0%BD%D0%BE%D0%B2%D1%8B%D1%85+%D1%84%D1%83%D0%BD%D0%BA%D1%86%D0%B8%D0%B9.+&target-visibility=on&project-name=Webcraftic&project-site=&button-text=05&comment=on&hint=%D0%9A%D0%B0%D0%BA%D1%83%D1%8E+%D1%84%D1%83%D0%BD%D0%BA%D1%86%D0%B8%D1%8E+%D0%BD%D1%83%D0%B6%D0%BD%D0%BE+%D0%B4%D0%BE%D0%B1%D0%B0%D0%B2%D0%B8%D1%82%D1%8C+%D0%B2+%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD%3F&mail=on&successURL="
				        width="508" height="187"></iframe>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Создает html разметку виджета поддержки
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.0.8
	 */
	public function showSupportWidget()
	{
		$free_support_url = $this->plugin->get_support()->get_contacts_url();
		$hot_support_url = $this->plugin->get_support()->get_site_url() . '/other-questions-support';

		?>
		<div id="wbcr-clr-support-widget" class="wbcr-factory-sidebar-widget">
			<p><strong><?php _e('Having Issues?', 'clearfy'); ?></strong></p>
			<div class="wbcr-clr-support-widget-body">
				<p>
					<?php _e('We provide free support for this plugin. If you are pushed with a problem, just create a new ticket. We will definitely help you!', 'clearfy'); ?>
				</p>
				<ul>
					<li><span class="dashicons dashicons-sos"></span>
						<a href="<?= $free_support_url ?>" target="_blank"
						   rel="noopener"><?php _e('Get starting free support', 'clearfy'); ?></a>
					</li>
					<li style="margin-top: 15px;background: #fff4f1;padding: 10px;color: #a58074;">
						<span class="dashicons dashicons-warning"></span>
						<?php printf(__('If you find a php error or a vulnerability in plugin, you can <a href="%s" target="_blank" rel="noopener">create ticket</a> in hot support that we responded instantly.', 'clearfy'), $hot_support_url); ?>
					</li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Registers page options in the options registry
	 *
	 * This will allow the user to search all the plugin options.
	 */
	public function register_options_to_search()
	{
		require_once FACTORY_CLEARFY_227_DIR . '/includes/class-search-options.php';

		$options = $this->getPageOptions();
		$page_url = $this->getBaseUrl();
		$page_id = $this->getResultId();

		\WBCR\Factory_Clearfy_227\Search_Options::register_options($options, $page_url, $page_id);
	}

	/**
	 * Add search plugin options form to each option page
	 */
	public function printAllNotices()
	{
		parent::printAllNotices(); // TODO: Change the autogenerated stub

		if( 'page' === $this->type || !$this->show_search_options_form ) {
			return;
		}
		?>
		<div id="wbcr-factory-clearfy-227__search_options_form" class="wbcr-factory-clearfy-227__autocomplete-wrap">
			<label for="autocomplete" class="wbcr-factory-clearfy-227__autocomplete-label">
				<?php _e('Can\'t find the settings you need? Use the search by the plugin options:', 'wbcr_factory_clearfy_227'); ?>
			</label>
			<input type="text" name="country" id="wbcr-factory-clearfy-227__autocomplete"/>

		</div>
		<?php
	}
}

