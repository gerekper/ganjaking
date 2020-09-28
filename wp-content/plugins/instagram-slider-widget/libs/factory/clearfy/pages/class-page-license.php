<?php

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Class Wbcr_FactoryLicense000_LicensePage is used as template to display form to active premium functionality.
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @since         2.0.7
 */
class Wbcr_FactoryClearfy227_LicensePage extends Wbcr_FactoryClearfy227_PageBase {

	/**
	 * {@inheritdoc}
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.1.2
	 * @var string
	 */
	public $type = "page";

	/**
	 * {@inheritdoc}
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.1.2
	 * @var string
	 */
	public $page_menu_dashicon = 'dashicons-admin-network';

	/**
	 * {@inheritdoc}
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.1.2
	 * @var bool
	 */
	public $show_right_sidebar_in_options = false;

	/**
	 * {@inheritdoc}
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.1.2
	 * @var int
	 */
	public $page_menu_position = 0;

	/**
	 * {@inheritdoc}
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.1.2
	 * @var bool
	 */
	public $available_for_multisite = true;

	/**
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  2.1.2
	 * @var string
	 */
	public $plugin_name;

	/**
	 * @var string Name of the paid plan.
	 */
	public $plan_name;

	// PREMIUM SECTION
	// ------------------------------------------------------------------
	/**
	 * @since 2.0.7
	 * @var bool
	 */
	protected $is_premium;

	/**
	 * @since 2.0.7
	 * @var \WBCR\Factory_436\Premium\Provider
	 */
	protected $premium;

	/**
	 * @since 2.0.7
	 * @var bool
	 */
	protected $is_premium_active;

	/**
	 * @since 2.0.7
	 * @var bool
	 */
	protected $premium_has_subscription;

	/**
	 * @since 2.0.7
	 * @var \WBCR\Factory_436\Premium\Interfaces\License
	 */
	protected $premium_license;

	// END PREMIUM SECTION
	// ------------------------------------------------------------------

	/**
	 * {@inheritdoc}
	 * @param Wbcr_Factory436_Plugin $plugin
	 */
	public function __construct(Wbcr_Factory436_Plugin $plugin)
	{
		$this->plugin = $plugin;

		parent::__construct($plugin);

		if( !$this->id ) {
			$this->id = $this->plugin->getPrefix() . 'license';
		}

		$this->plugin_name = $this->plugin->getPluginName();
		$this->premium = $plugin->premium;
		$this->is_premium = $this->premium->is_activate();
		$this->is_premium_active = $this->premium->is_active();
		$this->premium_has_subscription = $this->premium->has_paid_subscription();
		$this->premium_license = $this->premium->get_license();

		add_action("wp_ajax_wbcr-clearfy-activate-license-for-{$this->plugin_name}", [$this, 'ajax_handler']);
	}

	/**
	 * [MAGIC] Magic method that configures assets for a page.
	 */
	public function assets($scripts, $styles)
	{
		parent::assets($scripts, $styles);

		$this->styles->add(FACTORY_CLEARFY_227_URL . '/assets/css/license-manager.css');
		$this->scripts->add(FACTORY_CLEARFY_227_URL . '/assets/js/clearfy-license-manager.js');
	}

	/**
	 * Регистрируем ajax обработчик для текущей страницы
	 *
	 * @since 2.0.7
	 */
	public function ajax_handler()
	{
		wbcr_factory_clearfy_227_check_license($this->plugin);
	}

	/**
	 * {@inheritdoc}
	 */
	public function showPageContent()
	{
		?>
		<div id="wcl-license-wrapper"
		     data-loader="<?php echo FACTORY_CLEARFY_227_URL . '/assets/img/loader.gif'; ?>"
		     data-plugin-name="<?php echo esc_attr($this->plugin_name); ?>"
		     data-nonce="<?php echo wp_create_nonce("clearfy_activate_license_for_{$this->plugin_name}") ?>">
			<?php $this->show_license_form(); ?>
		</div>
		<?php
	}

	/**
	 * Get before content.
	 *
	 * @return string Before content.
	 */
	protected function get_plan_description()
	{
		return '';
	}

	/**
	 * @return string
	 */
	protected function get_hidden_license_key()
	{
		if( !$this->is_premium ) {
			return '';
		}

		return $this->premium_license->get_hidden_key();
	}

	/**
	 * @return string
	 */
	protected function get_plan()
	{
		if( !$this->is_premium ) {
			return 'free';
		}

		return $this->premium->get_plan();
	}

	/**
	 * @return mixed
	 */
	protected function get_expiration_days()
	{
		if( !$this->is_premium ) {
			return '';
		}

		return $this->premium_license->get_expiration_time('days');
	}

	/**
	 * @return string
	 */
	protected function get_billing_cycle_readable()
	{
		if( !$this->is_premium ) {
			return '';
		}

		$billing_cycle = $this->premium->get_billing_cycle();
		$billing = 'lifetime';

		if( 1 == $billing_cycle ) {
			$billing = 'month';
		} else if( 12 == $billing_cycle ) {
			$billing = 'year';
		}

		return $billing;
	}

	/**
	 * Тип лицензии, цветовое оформление для формы лицензирования
	 * free - бесплатная
	 * gift - пожизненная лицензия, лицензия на особых условиях
	 * trial - красный цвет, применяется для триалов, если лиценизия истекла или заблокирована
	 * paid - обычная оплаченная лицензия, в данный момент активна.
	 *
	 * @return string
	 */
	protected function get_license_type()
	{
		if( !$this->is_premium ) {
			return 'free';
		}

		$license = $this->premium_license;

		if( $license->is_lifetime() ) {
			return 'gift';
		} else if( $license->get_expiration_time('days') < 1 ) {
			return 'trial';
		}

		return 'paid';
	}

	protected function render_learnmore_section()
	{
		if( $this->is_premium ):
			?>
			<p style="margin-top: 10px;">
				<?php printf(__('<a href="%s" target="_blank" rel="noopener">Lean more</a> about the premium version and get the license key to activate it now!', 'wbcr_factory_clearfy_227'), $this->plugin->get_support()->get_pricing_url(true, 'license_page')); ?>
			</p>
		<?php else: ?>
			<p style="margin-top: 10px;">
				<?php printf(__('Can’t find your key? Go to <a href="%s" target="_blank" rel="noopener">this page</a> and login using the e-mail address associated with your purchase.', 'wbcr_factory_clearfy_227'), $this->plugin->get_support()->get_contacts_url(true, 'license_page')) ?>
			</p>
		<?php endif;
	}

	/**
	 * @param bool|WP_Error $notice
	 */
	public function show_license_form($notice = false)
	{
		?>
		<div id="license-manager"
		     class="factory-bootstrap-436 onp-page-wrap <?= $this->get_license_type() ?>-license-manager-content">
			<div>
				<h3><?php printf(__('Activate %s', 'wbcr_factory_clearfy_227'), $this->plan_name) ?></h3>
				<?php echo $this->get_plan_description() ?>
			</div>
			<br>
			<?php if( is_wp_error($notice) ) : ?>
				<div class="license-message <?= $this->get_license_type() ?>-license-message">
					<div class="alert <?php echo esc_attr($notice->get_error_code()); ?>">
						<h4 class="alert-heading"><?php _e($notice->get_error_message(), 'wbcr_factory_clearfy_227') ?></h4>
					</div>
				</div>
			<?php endif; ?>
			<div class="onp-container">
				<div class="license-details">
					<?php if( $this->get_license_type() == 'free' ): ?>
						<a href="<?php echo $this->plugin->get_support()->get_pricing_url(true, 'license_page'); ?>"
						   class="purchase-premium" target="_blank" rel="noopener">
                            <span class="btn btn-gold btn-inner-wrap">
                            <?php _e('Upgrade to Premium', 'wbcr_factory_clearfy_227') ?>
                            </span>
						</a>
						<p><?php printf(__('Your current license for %1$s:', 'wbcr_factory_clearfy_227'), $this->plugin->getPluginTitle()) ?></p>
					<?php endif; ?>
					<div class="license-details-block <?= $this->get_license_type() ?>-details-block">
						<?php if( $this->is_premium ): ?>
							<a data-action="deactivate" href="#"
							   class="btn btn-default btn-small license-delete-button wcl-control-btn">
								<?php _e('Delete Key', 'wbcr_factory_clearfy_227') ?>
							</a>
							<a data-action="sync" href="#"
							   class="btn btn-default btn-small license-synchronization-button wcl-control-btn">
								<?php _e('Synchronization', 'wbcr_factory_clearfy_227') ?>
							</a>
						<?php endif; ?>
						<h3>
							<?php echo ucfirst($this->get_plan()); ?>

							<?php if( $this->is_premium && $this->premium_has_subscription ): ?>
								<span style="font-size: 15px;">
                                    (<?php printf(__('Automatic renewal, every %s', ''), esc_attr($this->get_billing_cycle_readable())); ?>)
                                </span>
							<?php endif; ?>
						</h3>
						<?php if( $this->is_premium ): ?>
							<div class="license-key-identity">
								<code><?= esc_attr($this->get_hidden_license_key()) ?></code>
							</div>
						<?php endif; ?>
						<div class="license-key-description">
							<?php if( !$this->is_premium ): ?>
								<p><?php _e('Public License is a GPLv3 compatible license allowing you to change and use this version of the plugin for free. Please keep in mind this license covers only free edition of the plugin. Premium versions are distributed with other type of a license.', 'wbcr_factory_clearfy_227') ?></p>
							<?php else: ?>
								<p><?php _e('Сommercial license, only to the premium add-on to this free plugin. You cannot distribute or modify the premium add-on. But free plugin is a GPLv3 compatible license allowing you to change and use this version of the plugin for free.', 'wbcr_factory_clearfy_227') ?></p>
							<?php endif; ?>
							<?php if( $this->is_premium && $this->premium_has_subscription ): ?>
								<p class="activate-trial-hint">
									<?php _e('You use a paid subscription for the plugin updates. In case you don’t want to receive paid updates, please, click <a data-action="unsubscribe" class="wcl-control-btn" href="#">cancel subscription</a>', 'wbcr_factory_clearfy_227') ?>
								</p>
							<?php endif; ?>

							<?php if( $this->get_license_type() == 'trial' ): ?>
								<p class="activate-error-hint">
									<?php printf(__('Your license has expired, please extend the license to get updates and support.', 'wbcr_factory_clearfy_227'), '') ?>
								</p>
							<?php endif; ?>
						</div>
						<table class="license-params" colspacing="0" colpadding="0">
							<tr>
								<!--<td class="license-param license-param-domain">
										<span class="license-value"><?php echo esc_attr($_SERVER['SERVER_NAME']); ?></span>
										<span class="license-value-name"><?php _e('domain', 'wbcr_factory_clearfy_227') ?></span>
									</td>-->
								<td class="license-param license-param-days">
									<span class="license-value"><?= $this->get_plan() ?></span>
									<span class="license-value-name"><?php _e('plan', 'wbcr_factory_clearfy_227') ?></span>
								</td>
								<?php if( $this->is_premium ) : ?>
									<td class="license-param license-param-sites">
                                        <span class="license-value">
                                            <?php echo esc_attr($this->premium_license->get_count_active_sites()); ?>
                                            <?php _e('of', 'wbcr_factory_clearfy_227') ?>
                                            <?php echo esc_attr($this->premium_license->get_sites_quota()); ?></span>
										<span class="license-value-name"><?php _e('active sites', 'wbcr_factory_clearfy_227') ?></span>
									</td>
								<?php endif; ?>
								<td class="license-param license-param-version">
									<span class="license-value"><?= $this->plugin->getPluginVersion() ?></span>
									<span class="license-value-name"><span>version</span></span>
								</td>
								<?php if( $this->is_premium ): ?>
									<td class="license-param license-param-days">
										<?php if( $this->get_license_type() == 'trial' ): ?>
											<span class="license-value"><?php _e('EXPIRED!', 'wbcr_factory_clearfy_227') ?></span>
											<span class="license-value-name"><?php _e('please update the key', 'wbcr_factory_clearfy_227') ?></span>
										<?php else: ?>
											<span class="license-value">
													<?php
													if( $this->premium_license->is_lifetime() ) {
														echo 'infiniate';
													} else {
														echo $this->get_expiration_days();
													}
													?>
                                                <small> <?php _e('day(s)', 'wbcr_factory_clearfy_227') ?></small>
                                             </span>
											<span class="license-value-name"><?php _e('remained', 'wbcr_factory_clearfy_227') ?></span>
										<?php endif; ?>
									</td>
								<?php endif; ?>
							</tr>
						</table>
					</div>
				</div>
				<div class="license-input">
					<form action="" method="post">
						<?php if($this->is_premium): ?>
					<p><?php _e('Have a key to activate the premium version? Paste it here:', 'wbcr_factory_clearfy_227') ?><p>
					<?php else: ?>
						<p><?php _e('Have a key to activate the plugin? Paste it here:', 'wbcr_factory_clearfy_227') ?>
						<p>
							<?php endif; ?>
							<button data-action="activate" class="btn btn-default wcl-control-btn" type="button"
							        id="license-submit">
								<?php _e('Submit Key', 'wbcr_factory_clearfy_227') ?>
							</button>
						<div class="license-key-wrap">
							<input type="text" id="license-key" name="licensekey" value="" class="form-control"/>
						</div>
						<?php $this->render_learnmore_section(); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}
