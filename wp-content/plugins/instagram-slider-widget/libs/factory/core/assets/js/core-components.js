/**
 * This code provides tools for downloading, installing external add-ons for the Clearfy plugin
 *
 * @author Alex Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 10.09.2017, Webcraftic
 * @version 1.0
 */

(function($) {
	'use strict';

	var externalAddon = {
		init: function() {
			this.events();
		},
		events: function() {
			var self = this;

			/**
			 * This event is intended for installation, removal, activation, deactivation of external add-ons
			 */

			$(document).on('click', '.wfactory-439-process-button', function() {
				var $this = $(this),
					button_i18n = $(this).data('i18n'),
					plugin_slug = $(this).data('slug'),
					plugin_action = $(this).data('plugin-action'),
					plugin = $(this).data('plugin'),
					storage = $(this).data('storage'),
					wpnonce = $(this).data('wpnonce');

				var action = ('creativemotion' === storage)
				             ? 'wfactory-439-creativemotion-install-plugin'
				             : 'install-plugin';

				if( storage === 'freemius' || ((storage === 'wordpress' || storage === 'creativemotion' || storage === 'internal') && (plugin_action === 'activate' || plugin_action === 'deactivate')) ) {
					action = 'wfactory-439-intall-component';
				} else if( storage === 'wordpress' && plugin_action === 'delete' ) {
					action = 'delete-plugin';
				}

				var data = {
					action: action,
					slug: plugin_slug,
					storage: storage,
					plugin: plugin,
					plugin_action: plugin_action,
					_wpnonce: wpnonce
				};

				if( plugin_action === 'install' ) {
					$this.addClass('updating-message');
				}

				$this.addClass('disabled').text(button_i18n.loading);

				$.wfactory_439.hooks.run('core/components/pre_update', [$this, data]);

				self.sendRequest(data, function(response) {
					if( !response || !response.success ) {
						$.wfactory_439.hooks.run('core/components/update_error', [
							$this,
							data,
							response
						]);

						return;
					}

					if( response.success ) {
						$this.removeClass('disabled').removeClass('updating-message');

						if( plugin_action === 'install' ) {

							plugin_action = 'activate';
							$this.data('plugin-action', 'activate');
							$this.attr('data-plugin-action', 'activate');

							if( $this.hasClass('button') ) {
								$this.removeClass('button-default').addClass('button-primary');
							}

							$.wfactory_439.hooks.run('core/components/installed', [
								$this,
								data,
								response
							]);

						} else if( plugin_action === 'activate' ) {

							plugin_action = 'deactivate';
							$this.data('plugin-action', 'deactivate');
							$this.attr('data-plugin-action', 'deactivate');

							if( $this.hasClass('button') ) {
								$this.removeClass('button-primary').addClass('button-default');
							}

							$.wfactory_439.hooks.run('core/components/pre_activate', [
								$this,
								data,
								response
							]);

							/**
							 * Send an additional request for activation of the component, during activation
							 * perform the action wbcr/clearfy/activated_component.
							 *
							 * Basically, this is necessary to prepare the plugin to work, write the necessary rows and
							 * tables in the database, rewriting permalinks, checking conflicts, etc.
							 */
							if( storage === 'freemius' || storage === 'internal' ) {
								self.sendRequestToComponentActivationPrepare($this, data, button_i18n);
								return;
							}

						} else if( plugin_action === 'deactivate' ) {

							plugin_action = 'activate';
							$this.data('plugin-action', 'activate');
							$this.attr('data-plugin-action', 'activate');

							if( $this.hasClass('button') ) {
								$this.removeClass('button-default').addClass('button-primary');
							}

							$.wfactory_439.hooks.run('core/components/deactivated', [
								$this,
								data,
								response
							]);

						} else if( plugin_action === 'delete' ) {

							plugin_action = 'install';

							$.wfactory_439.hooks.run('core/components/deleted', [$this, data, response]);
						}
					} else {
						if( plugin_action === 'install' ) {
							$this.removeClass('updating-message');
						}
					}

					$this.text(button_i18n[plugin_action]);

					$.wfactory_439.hooks.run('core/components/updated', [$this, data, response]);
				});

				return false;
			});
		},

		/**
		 * Отправляет дополнительный запрос на активацию компонента, во время активации
		 * выполняет хук wbcr/clearfy/activated_component.
		 *
		 * В принципе, это необходимо для подготовки плагина к работе, записи необходимых строк и таблиц в
		 * базу данных, перепись постоянных ссылок, проверка конфликтов и т.д.
		 *
		 * @param {object} componentButton
		 * @param {object} sendData
		 * @param {object} button_i18n
		 */
		sendRequestToComponentActivationPrepare: function(componentButton, sendData, button_i18n) {
			var self = this;

			componentButton.addClass('button-primary')
				.addClass('disabled')
				.text(button_i18n.preparation);

			sendData.action = 'wfactory-439-prepare-component';

			this.sendRequest(sendData, function(response) {
				componentButton.removeClass('disabled');

				if( !response || !response.success ) {
					componentButton.text(button_i18n['activate']);

					$.wfactory_439.hooks.run('core/components/activation_error', [
						componentButton,
						sendData,
						response
					]);
					return;
				}

				componentButton.removeClass('button-primary').text(button_i18n['deactivate']);

				$.wfactory_439.hooks.run('core/components/activated', [
					componentButton,
					sendData,
					response
				]);
			});
		},

		sendRequest: function(data, callback) {
			var self = this;

			$.ajax(ajaxurl, {
				type: 'post',
				dataType: 'json',
				data: data,
				success: function(data, textStatus, jqXHR) {
					callback && callback(data);
				},
				error: function(xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(xhr.responseText);
					console.log(thrownError);

					$.wfactory_439.hooks.run('core/components/ajax_error', [
						xhr,
						ajaxOptions,
						thrownError
					]);
				}
			});
		}
	};

	$(document).ready(function() {
		externalAddon.init();
	});

})(jQuery);
