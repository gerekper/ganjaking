<?php
/**
 * Impressive page themplate class
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>
 * @since         1.0.0
 * @package       factory-pages
 * @copyright (c) 2018, Webcraftic Ltd
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wbcr_FactoryPages423_ImpressiveThemplate' ) ) {
	/**
	 * Class Wbcr_FactoryPages423_ImpressiveThemplate
	 */
	abstract class Wbcr_FactoryPages423_ImpressiveThemplate extends Wbcr_FactoryPages423_AdminPage {

		/**
		 * Тип страницы:
		 *
		 * Существует только два типа страниц:
		 * options - предназначена для создании страниц с набором опций и настроек.
		 * page - предназначена для произвольного контента, любой html код.
		 *
		 * @var string
		 */
		public $type = 'options';

		/**
		 * ID родительской страницы, если указан, тогда эта страница будет внутренней странтцей
		 *
		 * @var string
		 */
		public $page_parent_page;

		/**
		 * Иконка меню в интерфейсе плагина
		 *
		 * Отображается в левом меню интерфейса плагина. Вы должны указать css класс dashicons.
		 * Иконки можно найти тут: https://developer.wordpress.org/resource/dashicons/#admin-site-alt
		 *
		 * @var string
		 */
		public $page_menu_dashicon;

		/**
		 * Короткое описание пункта меню в интерфейсе плагина
		 *
		 * Используется для справки, что содержится в разделе меню. По умолчанию
		 * описание не используется.
		 *
		 * @var string
		 */
		public $page_menu_short_description;

		/**
		 * Позиция закладки на страницу в главном меню плагина
		 *
		 * Может быть полезно, если вы хотите, что закладка на вашу страницу
		 * была выше всех остальных закладок.
		 * Вы должны установить число от 0 до 100, чем выше число, тем ниже
		 * будет расположена закладка в меню.
		 *
		 * Пример: 0 - в самом конце, 100 - в самом начале
		 *
		 * @var int
		 */
		public $page_menu_position = 10;

		/**
		 * Показывать заголовок страницы?
		 *
		 * Заголовок страницы печатается в шапке интерфейса плагина, по некоторым
		 * причинам вам может понадобиться его скрыть. Установите false, чтобы скрыть
		 * заголовок страницы.
		 *
		 * @var bool
		 */
		public $show_page_title = true;

		/**
		 * Показывать правый сайдбар в контенте страницы?
		 *
		 * Сайдбар для рекламы, уведомлений, виджетов. Если вы планируете
		 * например выводить рекламу для покупки премиум версии плагина,
		 * вы можете включить правый сайдбар и вставить в него виджет с рекламой.
		 *
		 * Внимание! Сайбар будет показан только на страницах с типом options.
		 *
		 * @var bool
		 */
		public $show_right_sidebar_in_options = false;

		/**
		 * Показывать нижний сайдбар в контенте страницы?
		 * Сайдбар для рекламы, уведомлений, виджетов. Если вы планируете
		 * например выводить рекламу для покупки премиум версии плагина,
		 * вы можете включить правый сайдбар и вставить в него виджет с рекламой.
		 *
		 * @var bool
		 */
		public $show_bottom_sidebar = true;

		/**
		 * @var array
		 */
		public $page_menu = [];

		/**
		 * @param Wbcr_Factory423_Plugin $plugin
		 */
		public function __construct( Wbcr_Factory423_Plugin $plugin ) {
			$this->menuIcon = FACTORY_PAGES_423_URL . '/templates/assets/img/webcraftic-plugin-icon.png';
			//$allow_multisite = apply_filters('wbcr_factory_423_core_admin_allow_multisite', false);

			if ( is_multisite() && $this->available_for_multisite && $plugin->isNetworkActive() ) {
				$this->network     = true;
				$this->menu_target = 'settings.php';
				$this->capabilitiy = 'manage_network';
			}

			parent::__construct( $plugin );

			$this->title_plugin_action_link = __( 'Settings', 'wbcr_factory_pages_423' );

			$this->setPageMenu();
		}

		/**
		 * Set page menu item
		 */
		public function setPageMenu() {
			global $factory_impressive_page_menu;

			$dashicon          = ( ! empty( $this->page_menu_dashicon ) ) ? ' ' . $this->page_menu_dashicon : '';
			$short_description = ( ! empty( $this->page_menu_short_description ) ) ? ' ' . $this->page_menu_short_description : '';

			if ( is_multisite() && is_network_admin() && ! $this->network ) {
				return;
			}

			$factory_impressive_page_menu[ $this->getMenuScope() ][ $this->getResultId() ] = [
				'type'              => $this->type, // page, options
				'url'               => $this->getBaseUrl(),
				'title'             => $this->getPageTitle() . ' <span class="dashicons' . $dashicon . '"></span>',
				'short_description' => $short_description,
				'position'          => $this->page_menu_position,
				'parent'            => $this->page_parent_page
			];
		}

		/**
		 * Get page menu items
		 *
		 * @return mixed
		 */
		public function getPageMenu() {
			global $factory_impressive_page_menu;

			return $factory_impressive_page_menu[ $this->getMenuScope() ];
		}

		/**
		 * Requests assets (js and css) for the page.
		 *
		 * @since 1.0.0
		 * @return void
		 * @see   FactoryPages423_AdminPage
		 *
		 */
		public function assets( $scripts, $styles ) {

			$this->scripts->request( 'jquery' );

			$this->scripts->request( [
				'control.checkbox',
				'control.dropdown',
				'bootstrap.tooltip'
			], 'bootstrap' );

			$this->styles->request( [
				'bootstrap.core',
				'bootstrap.form-group',
				'bootstrap.separator',
				'control.dropdown',
				'control.checkbox'
			], 'bootstrap' );

			$this->styles->add( FACTORY_PAGES_423_URL . '/templates/assets/css/impressive.page.template.css' );
		}

		/**
		 * Получает заголовок плагина (обычно используется для брендинга)
		 *
		 * @return string
		 */
		public function getPluginTitle() {
			/**
			 * @since 4.0.8 - добавлен
			 * @since 4.0.9 - является устаревшим
			 */
			$plugin_title = wbcr_factory_423_apply_filters_deprecated( 'wbcr/factory/imppage/plugin_title', [
				$this->plugin->getPluginTitle(),
				$this->plugin->getPluginName()
			], '4.0.9', 'wbcr/factory/pages/impressive/plugin_title' );

			/**
			 * @since 4.0.9 - является устаревшим
			 */
			$plugin_title = apply_filters( 'wbcr/factory/pages/impressive/plugin_title', $plugin_title, $this->plugin->getPluginName() );

			return $plugin_title;
		}

		/**
		 * Получает полную ссылку текущей страницы
		 *
		 * @return string
		 */
		public function getPageUrl() {
			/**
			 * @since 4.0.9 - добавлен
			 */
			return apply_filters( 'wbcr/factory/pages/impressive/base_url', $this->getBaseUrl(), $this->plugin->getPluginName(), $this->id );
		}

		/**
		 * Пространство имен для меню плагина
		 * Можно приклеить меню к другому плагину, просто перезаписав этот метод в дочернем классе
		 *
		 * @return string
		 */
		public function getMenuScope() {
			/**
			 * @since 4.0.9 - добавлен
			 */
			return apply_filters( 'wbcr/factory/pages/impressive/menu_scope', $this->plugin->getPluginName(), $this->plugin->getPluginName(), $this->id );
		}

		/**
		 * Get options with namespace
		 *
		 * @param      $option_name
		 * @param bool $default
		 *
		 * @return mixed|void
		 */
		public function getOption( $option_name, $default = false ) {
			_deprecated_function( __METHOD__, '4.0.9', '$this->plugin->getOption()' );

			return $this->plugin->getOption( $option_name, $default );
		}

		/**
		 * Shows a page or options
		 *
		 * @sinve 1.0.0
		 * @return void
		 */
		public function indexAction() {
			$page_menu = $this->getPageMenu();
			if ( 'options' === $page_menu[ $this->getResultId() ]['type'] ) {
				$this->showOptions();
			} else {
				$this->showPage();
			}
		}

		/**
		 * Flush cache and rules
		 *
		 * @sinve 4.0.0
		 * @return void
		 */
		public function flushCacheAndRulesAction() {
			check_admin_referer( 'wbcr_factory_' . $this->getResultId() . '_flush_action' );

			if ( class_exists( 'WbcrFactoryClearfy000_Helpers' ) ) {
				WbcrFactoryClearfy000_Helpers::flushPageCache();
			}

			/**
			 * @since 4.0.1 - является устаревшим
			 */
			wbcr_factory_423_do_action_deprecated( 'wbcr_factory_423_imppage_flush_cache', [
				$this->plugin->getPluginName(),
				$this->getResultId()
			], '4.0.1', 'wbcr_factory_423_imppage_after_form_save' );

			/**
			 * @since 4.0.9 - является устаревшим
			 */
			wbcr_factory_423_do_action_deprecated( 'wbcr_factory_423_imppage_after_form_save', [
				$this->plugin,
				$this
			], '4.0.9', 'wbcr/factory/pages/impressive/after_form_save' );

			/**
			 * @since 4.0.1 - добавлен
			 * @since 4.0.9 - изменено название экшена, без привязки к версии фреймворка
			 */
			do_action( 'wbcr/factory/pages/impressive/after_form_save', $this->plugin, $this );

			$this->afterFormSave();

			$redirect_args = [
				$this->plugin->getPluginName() . '_saved' => 1
			];
			/**
			 * @since 4.0.9 - является устаревшим
			 */
			$redirect_args = wbcr_factory_423_apply_filters_deprecated( 'wbcr_factory_423_imppage_after_form_save_redirect_args', [ $redirect_args ], '4.0.9', 'wbcr/factory/pages/impressive/save_redirect_args' );

			/**
			 * @since 4.0.1 - добавлен
			 * @since 4.0.9 - изменено название экшена, без привязки к версии фреймворка
			 */
			$redirect_args = apply_filters( 'wbcr/factory/pages/impressive/save_redirect_args', $redirect_args );

			$this->redirectToAction( 'index', $redirect_args );
		}


		/**
		 * Вызывается всегда при загрузке страницы, перед опциями формы с типом страницы options
		 */
		protected function warningNotice() {
			/*if( WP_CACHE ) {
				$this->printWarningNotice(__("It seems that a caching/performance plugin is active on this site. Please manually invalidate that plugin's cache after making any changes to the settings below.", 'wbcr_factory_pages_423'));
			}*/
			// Метод предназначен для вызова в дочернем классе
		}

		/**
		 * Вызывается всегда при загрузке страницы, перед опциями формы с типом страницы options
		 *
		 * @since 4.0.0
		 *
		 * @param array $notices
		 *
		 * @return array
		 */
		protected function getActionNotices( $notices ) {
			// Метод предназначен для вызова в дочернем классе
			return $notices;
		}

		/**
		 * Вызывается перед сохранением опций формы
		 *
		 * @since 4.0.0
		 * @return void
		 */
		protected function beforeFormSave() {
			// Метод предназначен для вызова в дочернем классе
		}

		/**
		 * Вызывается после сохранением опций формы, когда выполнен сброс кеша и совершен редирект
		 *
		 * @since 4.0.0
		 * @return void
		 */
		protected function afterFormSave() {
			// Метод предназначен для вызова в дочернем классе
		}

		/**
		 * Вызывается в процессе выполнения сохранения, но после сохранения всех опций
		 *
		 * @since 4.0.0
		 * @return void
		 */
		protected function formSaved() {
			// Метод предназначен для вызова в дочернем классе
		}

		public function printWarningNotice( $message ) {
			echo '<div class="alert alert-warning wbcr-factory-warning-notice"><p><span class="dashicons dashicons-warning"></span> ' . $message . '</p></div>';
		}

		public function printErrorNotice( $message ) {
			echo '<div class="alert alert-danger wbcr-factory-warning-notice"><p><span class="dashicons dashicons-dismiss"></span> ' . $message . '</p></div>';
		}

		public function printSuccessNotice( $message ) {
			echo '<div class="alert alert-success wbcr-factory-warning-notice"><p><span class="dashicons dashicons-plus"></span> ' . $message . '</p></div>';
		}

		/**
		 * Печатает все зарегистрированные системные уведомления внутри интерфейса плагина
		 * Типы уведомлений: предупреждения, ошибки, успешные выполнения задач
		 */
		protected function printAllNotices() {
			$this->warningNotice();
			$this->showActionsNotice();

			/**
			 * @since 4.0.9 - является устаревшим
			 */
			wbcr_factory_423_do_action_deprecated( 'wbcr_factory_pages_423_imppage_print_all_notices', [
				$this->plugin,
				$this
			], '4.0.9', 'wbcr/factory/pages/impressive/print_all_notices' );

			/**
			 * @since 4.0.1 - добавлен
			 * @since 4.0.9 - изменено имя
			 */
			do_action( 'wbcr/factory/pages/impressive/print_all_notices', $this->plugin, $this );
		}

		private function showActionsNotice() {
			$notices = [
				[
					'conditions' => [
						$this->plugin->getPluginName() . '_saved' => '1'
					],
					'type'       => 'success',
					'message'    => __( 'The settings have been updated successfully!', 'wbcr_factory_pages_423' ) . ( WP_CACHE ? '<br>' . __( "It seems that a caching/performance plugin is active on this site. Please manually invalidate that plugin's cache after making any changes to the settings below.", 'wbcr_factory_pages_423' ) : '' )
				]
			];

			/**
			 * @since 4.0.9 - является устаревшим
			 */
			$notices = wbcr_factory_423_apply_filters_deprecated( 'wbcr_factory_pages_423_imppage_actions_notice', [
				$notices,
				$this->plugin,
				$this->id
			], '4.0.9', 'wbcr/factory/pages/impressive/actions_notice' );

			/**
			 * @since 4.0.1 - добавлен
			 * @since 4.0.9 - изменено имя
			 */
			$notices = apply_filters( 'wbcr/factory/pages/impressive/actions_notice', $notices, $this->plugin, $this->id );

			$notices = $this->getActionNotices( $notices );

			foreach ( $notices as $key => $notice ) {
				$show_message = true;

				if ( isset( $notice['conditions'] ) && ! empty( $notice['conditions'] ) ) {
					foreach ( $notice['conditions'] as $condition_name => $value ) {
						if ( ! isset( $_REQUEST[ $condition_name ] ) || $_REQUEST[ $condition_name ] != $value ) {
							$show_message = false;
						}
					}
				}
				if ( ! $show_message ) {
					continue;
				}

				$notice_type = isset( $notice['type'] ) ? $notice['type'] : 'success';

				switch ( $notice_type ) {
					case 'success':
						$this->printSuccessNotice( $notice['message'] );
						break;
					case 'danger':
						$this->printErrorNotice( $notice['message'] );
						break;
					default:
						$this->printWarningNotice( $notice['message'] );
						break;
				}
			}
		}

		protected function showPageMenu() {
			$page_menu    = $this->getPageMenu();
			$self_page_id = $this->getResultId();
			$current_page = isset( $page_menu[ $self_page_id ] ) ? $page_menu[ $self_page_id ] : null;

			$parent_page_id = ! empty( $current_page['parent'] ) ? $this->getResultId( $current_page['parent'] ) : null;

			uasort( $page_menu, [ $this, 'pageMenuSort' ] );
			?>
            <ul>
				<?php foreach ( $page_menu as $page_screen => $page ): ?>
					<?php
					if ( ! empty( $page['parent'] ) ) {
						continue;
					}
					$active_tab = '';
					if ( $page_screen == $self_page_id || $page_screen == $parent_page_id ) {
						$active_tab = ' wbcr-factory-active-tab';
					}
					?>
                    <li class="wbcr-factory-nav-tab<?= $active_tab ?>">
                        <a href="<?php echo $page['url'] ?>" id="<?= $page_screen ?>-tab" class="wbcr-factory-tab__link">
                            <div class="wbcr-factory-tab__title">
								<?php echo $page['title'] ?>
                            </div>
							<?php if ( ! empty( $page['short_description'] ) ): ?>
                                <div class="wbcr-factory-tab__short-description">
									<?php echo $page['short_description'] ?>
                                </div>
							<?php endif; ?>
                        </a>
                    </li>
				<?php endforeach; ?>
            </ul>
			<?php
		}

		/**
		 * @param int $a
		 * @param int $b
		 *
		 * @return bool
		 */
		protected function pageMenuSort( $a, $b ) {
			return $a['position'] < $b['position'];
		}

		protected function showPageSubMenu() {
			$self_page_id = $this->getResultId();
			$page_menu    = $this->getPageMenu();
			$current_page = isset( $page_menu[ $self_page_id ] ) ? $page_menu[ $self_page_id ] : null;

			$page_submenu = [];
			foreach ( $page_menu as $page_screen => $page ) {
				if ( ! empty( $page['parent'] ) ) {
					$page_parent_id = $this->getResultId( $page['parent'] );

					if ( isset( $page_menu[ $page_parent_id ] ) ) {
						$page['title']                                   = strip_tags( $page['title'] );
						$page_submenu[ $page_parent_id ][ $page_screen ] = $page;
					}
				}
			}

			if ( empty( $page_submenu ) ) {
				return;
			}

			$get_menu_id    = null;
			$has_parent     = ! empty( $current_page ) && ! empty( $current_page['parent'] );
			$parent_page_id = $has_parent ? $this->getResultId( $current_page['parent'] ) : null;

			if ( ( $has_parent && isset( $page_submenu[ $parent_page_id ] ) ) ) {
				$get_menu_id = $parent_page_id;
			} else if ( ! $has_parent && isset( $page_submenu[ $self_page_id ] ) ) {
				$get_menu_id = $self_page_id;
			}

			if ( ! isset( $page_submenu[ $get_menu_id ] ) ) {
				return;
			}

			$unshift = [];
			if ( isset( $page_menu[ $get_menu_id ] ) ) {
				$page_menu[ $get_menu_id ]['title'] = strip_tags( $page_menu[ $get_menu_id ]['title'] );

				$unshift[ $get_menu_id ][ $get_menu_id ] = $page_menu[ $get_menu_id ];
				$page_submenu[ $get_menu_id ]            = $unshift[ $get_menu_id ] + $page_submenu[ $get_menu_id ];
			}

			?>
            <h2 class="nav-tab-wrapper wp-clearfix">
				<?php foreach ( (array) $page_submenu[ $get_menu_id ] as $page_screen => $page ): ?>
					<?php
					$active_tab = '';
					if ( $page_screen == $this->getResultId() ) {
						$active_tab = ' nav-tab-active';
					}
					?>
                    <a href="<?php echo $page['url'] ?>" id="<?= esc_attr( $page_screen ) ?>-tab" class="nav-tab<?= esc_attr( $active_tab ) ?>">
						<?php echo $page['title'] ?>
                    </a>
				<?php endforeach; ?>
            </h2>
			<?php
		}

		protected function showHeader() {
			?>
            <style>
                .updated, .notice, .error {
                    display: none !important;
                }
            </style>
            <div class="wbcr-factory-page-header">
                <div class="wbcr-factory-header-logo"><?= $this->getPluginTitle(); ?>
                    <span class="version"><?= $this->plugin->getPluginVersion() ?> </span>
					<?php if ( $this->show_page_title ): ?><span class="dash">—</span><?php endif; ?>
                </div>
				<?php if ( $this->show_page_title ): ?>
                    <div class="wbcr-factory-header-title">
                        <h2><?php _e( 'Page' ) ?>: <?= $this->getPageTitle() ?></h2>
                    </div>
				<?php endif; ?>
                <div class="wbcr-factory-control">
					<?php do_action( 'wbcr/factory/pages/impressive/header', $this->plugin->getPluginName() ) ?>

					<?php if ( $this->type == 'options' ): ?>
                        <input name="<?= $this->plugin->getPluginName() ?>_save_action" class="wbcr-factory-button wbcr-factory-type-save" type="submit" value="<?php _e( 'Save', 'wbcr_factory_pages_423' ); ?>">
						<?php wp_nonce_field( 'wbcr_factory_' . $this->getResultId() . '_save_action' ); ?>
					<?php endif; ?>
                </div>
            </div>
			<?php
		}

		protected function isShowRightSidebar() {
			$widgets = $this->getPageWidgets( 'right' );

			return ! empty( $widgets ) && $this->show_right_sidebar_in_options;
		}

		protected function showRightSidebar() {
			$widgets = $this->getPageWidgets( 'right' );

			if ( empty( $widgets ) ) {
				return;
			}

			foreach ( $widgets as $widget_content ):
				echo $widget_content;
			endforeach;
		}

		protected function showBottomSidebar() {
			$widgets = $this->getPageWidgets( 'bottom' );

			if ( empty( $widgets ) ) {
				return;
			}
			?>
            <div class="row">
            <div class="wbcr-factory-top-sidebar">
				<?php foreach ( $widgets as $widget_content ): ?>
                    <div class="col-sm-4">
						<?= $widget_content ?>
                    </div>
				<?php endforeach; ?>
            </div>
			<?php
		}

		/**
		 * @param string $position
		 *
		 * @return mixed|void
		 */
		protected function getPageWidgets( $position = 'bottom' ) {
			$widgets = [];

			/**
			 * @since 4.0.9 - является устаревшим
			 */
			$widgets = wbcr_factory_423_apply_filters_deprecated( 'wbcr_factory_pages_423_imppage_get_widgets', [
				$widgets,
				$position,
				$this->plugin,
				$this
			], '4.0.9', 'wbcr/factory/pages/impressive/widgets' );

			/**
			 * @since 4.0.1 - добавлен
			 * @since 4.0.9 - изменено имя
			 */
			$widgets = apply_filters( 'wbcr/factory/pages/impressive/widgets', $widgets, $position, $this->plugin, $this );

			return $widgets;
		}

		protected function showOptions() {
			$form = new Wbcr_FactoryForms421_Form( [
				'scope' => rtrim( $this->plugin->getPrefix(), '_' ),
				'name'  => $this->getResultId() . "-options"
			], $this->plugin );

			$form->setProvider( new Wbcr_FactoryForms421_OptionsValueProvider( $this->plugin ) );

			$options = $this->getPageOptions();

			if ( isset( $options[0] ) && isset( $options[0]['items'] ) && is_array( $options[0]['items'] ) ) {
				foreach ( $options[0]['items'] as $key => $value ) {

					if ( $value['type'] == 'div' ) {
						if ( isset( $options[0]['items'][ $key ]['items'] ) && ! empty( $options[0]['items'][ $key ]['items'] ) ) {
							foreach ( $options[0]['items'][ $key ]['items'] as $group_key => $group_value ) {
								$options[0]['items'][ $key ]['items'][ $group_key ]['layout']['column-left']  = '4';
								$options[0]['items'][ $key ]['items'][ $group_key ]['layout']['column-right'] = '8';
							}

							continue;
						}
					}

					if ( in_array( $value['type'], [
						'checkbox',
						'textarea',
						'integer',
						'textbox',
						'dropdown',
						'list',
						'wp-editor'
					] ) ) {
						$options[0]['items'][ $key ]['layout']['column-left']  = '4';
						$options[0]['items'][ $key ]['layout']['column-right'] = '8';
					}
				}
			}

			$form->add( $options );

			if ( isset( $_POST[ $this->plugin->getPluginName() . '_save_action' ] ) ) {

				check_admin_referer( 'wbcr_factory_' . $this->getResultId() . '_save_action' );

				if ( ! current_user_can( 'administrator' ) && ! current_user_can( $this->capabilitiy ) ) {
					wp_die( __( 'You do not have permission to edit page.', 'wbcr_factory_pages_423' ) );
					exit;
				}

				/**
				 * @since 4.0.9 - является устаревшим
				 */
				wbcr_factory_423_do_action_deprecated( 'wbcr_factory_423_imppage_before_form_save', [
					$form,
					$this->plugin,
					$this
				], '4.0.9', 'wbcr/factory/pages/impressive/before_form_save' );

				/**
				 * @since 4.0.1 - добавлен
				 * @since 4.0.9 - изменено имя
				 */
				do_action( 'wbcr/factory/pages/impressive/before_form_save', $form, $this->plugin, $this );

				$this->beforeFormSave();

				$form->save();

				/**
				 * @since 4.0.9 - является устаревшим
				 */
				wbcr_factory_423_do_action_deprecated( 'wbcr_factory_423_imppage_form_saved', [
					$form,
					$this->plugin,
					$this
				], '4.0.9', 'wbcr/factory/pages/impressive/form_saved' );

				/**
				 * @since 4.0.1 - добавлен
				 * @since 4.0.9 - изменено имя
				 */
				do_action( 'wbcr/factory/pages/impressive/form_saved', $form, $this->plugin, $this );

				$this->formSaved();

				$this->redirectToAction( 'flush-cache-and-rules', [
					'_wpnonce' => wp_create_nonce( 'wbcr_factory_' . $this->getResultId() . '_flush_action' )
				] );
			}

			?>
            <div id="WBCR" class="wrap">
                <div class="wbcr-factory-pages-423-impressive-page-template factory-bootstrap-424 factory-fontawesome-000">
                    <div class="wbcr-factory-options wbcr-factory-options-<?= esc_attr( $this->id ) ?>">
                        <div class="wbcr-factory-left-navigation-bar">
							<?php $this->showPageMenu() ?>
                        </div>
						<?php
						$min_height = 0;
						foreach ( $this->getPageMenu() as $page ) {
							if ( ! isset( $page['parent'] ) || empty( $page['parent'] ) ) {
								$min_height += 77;
							}
						}
						?>
                        <div class="wbcr-factory-page-inner-wrap">
                            <div class="wbcr-factory-content-section<?php if ( ! $this->isShowRightSidebar() ): echo ' wbcr-fullwidth'; endif ?>">
								<?php $this->showPageSubMenu() ?>
                                <div class="wbcr-factory-content" style="min-height:<?= $min_height ?>px">
                                    <form method="post" class="form-horizontal">
										<?php $this->showHeader(); ?>
										<?php $this->printAllNotices(); ?>
										<?php $form->html(); ?>
                                    </form>
                                </div>
                            </div>
							<?php if ( $this->isShowRightSidebar() ): ?>
                                <div class="wbcr-factory-right-sidebar-section">
									<?php $this->showRightSidebar(); ?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
					<?php
					if ( $this->show_bottom_sidebar ) {
						$this->showBottomSidebar();
					}
					?>
                    <div class="clearfix"></div>
                </div>
            </div>
            </div>
			<?php
		}

		protected function showPage( $content = null ) { ?>
            <div id="WBCR" class="wrap">
                <div class="wbcr-factory-pages-423-impressive-page-template factory-bootstrap-424 factory-fontawesome-000">
                    <div class="wbcr-factory-page wbcr-factory-page-<?= $this->id ?>">
						<?php $this->showHeader(); ?>
                        <div class="wbcr-factory-left-navigation-bar">
							<?php $this->showPageMenu() ?>
                        </div>
						<?php
						$min_height = 0;
						foreach ( $this->getPageMenu() as $page ) {
							if ( ! isset( $page['parent'] ) || empty( $page['parent'] ) ) {
								$min_height += 77;
							}
						}
						?>
                        <div class="wbcr-factory-page-inner-wrap">
                            <div class="wbcr-factory-content-section<?php if ( ! $this->isShowRightSidebar() ): echo ' wbcr-fullwidth'; endif ?>">
								<?php $this->showPageSubMenu() ?>
                                <div class="wbcr-factory-content" style="min-height:<?= $min_height ?>px">
									<?php $this->printAllNotices(); ?>
									<?php if ( empty( $content ) ): ?>
										<?php $this->showPageContent() ?>
									<?php else: ?>
										<?php echo $content; ?>
									<?php endif; ?>
                                </div>
                            </div>
							<?php if ( $this->isShowRightSidebar() ): ?>
                                <div class="wbcr-factory-right-sidebar-section" style="min-height:<?= $min_height ?>px">
									<?php $this->showRightSidebar(); ?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
					<?php $this->showBottomSidebar(); ?>
                </div>
            </div>
			<?php
		}

		/**
		 * @return array
		 */
		public function getPageOptions() {
			// используется в классе потомке
		}

		/**
		 * @return void
		 */
		public function showPageContent() {
			// используется в классе потомке
		}

		/**
		 * Shows the html block with a confirmation dialog.
		 *
		 * @sinve 1.0.0
		 * @return void
		 */
		public function confirmPageTemplate( $data ) {
			?>
            <div id="WBCR" class="wrap">
                <div class="wbcr-factory-pages-423-impressive-page-template factory-bootstrap-424 factory-fontawesome-000">
                    <div id="wbcr-factory-confirm-dialog">
                        <h2><?php echo $data['title'] ?></h2>
                        <p class="wbcr-factory-confirm-description"><?php echo $data['description'] ?></p>
						<?php if ( isset( $data['hint'] ) ): ?>
                            <p class="wbcr-factory-confirm-hint"><?php echo $data['hint'] ?></p>
						<?php endif; ?>
                        <div class='wbcr-factory-confirm-actions'>
							<?php foreach ( $data['actions'] as $action ) { ?>
                                <a href='<?php echo $action['url'] ?>' class='<?php echo $action['class'] ?>'>
									<?php echo $action['title'] ?>
                                </a>
							<?php } ?>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}
	}
}

