<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the License group.
 *
 * Expects $stateKey.
 *
 * @var string $stateKey The key under which the collapse state is stored.
 * @var bool $collapseable If defined, specifies whether or not this grouping can be collapsed. Defaults to true.
 */

if (!isset($collapseable)) {
	$collapseable = true;
}
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block<?php if (!$collapseable) { echo ' wf-always-active'; } else { echo (wfPersistenceController::shared()->isActive($stateKey) ? ' wf-active' : ''); } ?>" data-persistence-key="<?php echo esc_attr($stateKey); ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php esc_html_e('Wordfence License', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure" role="checkbox" aria-checked="<?php echo (wfPersistenceController::shared()->isActive($stateKey) ? 'true' : 'false'); ?>" tabindex="0"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<ul class="wf-flex-vertical wf-flex-full-width wf-add-top wf-add-bottom">
							<li><strong><?php esc_html_e('Your Wordfence License', 'wordfence'); ?></strong></li>
							<li>
								<ul id="wf-option-apiKey" class="wf-option wf-option-text" data-text-option="apiKey" data-original-text-value="<?php echo esc_attr(wfConfig::get('apiKey')); ?>">
									<li class="wf-option-title">
										<?php esc_html_e('License Key', 'wordfence'); ?> <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_API_KEY); ?>"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i><span class="screen-reader-text"> (<?php esc_html_e('opens in new tab', 'wordfence') ?>)</span></a>
									</li>
									<li class="wf-option-text wf-option-full-width wf-no-right">
										<input type="text" value="<?php echo esc_attr(wfConfig::get('apiKey')); ?>" id="wf-license-input">
									</li>
								</ul>
							</li>
							<li>
								<ul class="wf-flex-horizontal wf-flex-vertical-xs wf-flex-full-width">
									<li><strong><?php esc_html_e('License Status:', 'wordfence'); ?></strong>
										<?php
										if (wfLicense::current()->hasConflict()) {
											esc_html_e('License already in use', 'wordfence');
										}
										else if (wfLicense::current()->isExpired()) {
											echo esc_html(sprintf(__('%s License Expired', 'wordfence'), wfLicense::current()->getTypeLabel(false)));
										}
										else if (wfLicense::current()->getKeyType() === wfLicense::KEY_TYPE_PAID_DELETED) {
											esc_html_e('Premium License Deactivated', 'wordfence');
										}
										else {
											echo esc_html(sprintf(__('%s License Active', 'wordfence'), wfLicense::current()->getTypeLabel()));
										}
										?>
									</li>
									<li class="wf-right wf-flex-vertical-xs wf-flex-align-left wf-left-xs wf-padding-add-top-xs" id="wf-license-controls">
										<?php if (wfLicense::current()->isAtLeastPremium() || wfLicense::current()->hasConflict()): ?>
											<a href="#" class="wf-downgrade-license" role="button"><?php esc_html_e('Reset site to a free license', 'wordfence'); ?></a>
										<?php endif ?>
										<?php if (wfLicense::current()->hasConflict()): ?>
											<a href="https://www.wordfence.com/gnl1optMngKysReset/licenses/" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-primary wf-btn-callout-subtle"><?php esc_html_e('Reset License', 'wordfence') ?><span class="screen-reader-text"> (<?php esc_html_e('opens in new tab', 'wordfence') ?>)</span></a>
										<?php elseif (wfLicense::current()->isPaidAndCurrent()): ?>
											<a href="https://www.wordfence.com/gnl1optMngKys/licenses/" target="_blank" rel="noopener noreferrer" class=""><?php echo esc_html_e('Click here to manage your Wordfence licenses', 'wordfence'); ?><span class="screen-reader-text"> (<?php esc_html_e('opens in new tab', 'wordfence') ?>)</span></a>
										<?php else: ?>
											<?php if (wfLicense::current()->getKeyType() === wfLicense::KEY_TYPE_PAID_DELETED): ?>
												<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-downgrade-license" role="button"><?php esc_html_e('Remove Invalid License', 'wordfence'); ?></a>&nbsp;&nbsp;
											<?php endif ?>
											<a href="https://www.wordfence.com/gnl1optUpgrade/products/pricing/" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-primary wf-btn-callout-subtle"><?php esc_html_e('Upgrade to Premium', 'wordfence'); ?><span class="screen-reader-text"> (<?php esc_html_e('opens in new tab', 'wordfence') ?>)</span></a>
										<?php endif ?>
										<a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle" style="display: none;" id="wf-install-license" role="button"><?php esc_html_e('Install License', 'wordfence'); ?></a>
									</li>
								</ul>
								
								<script type="application/javascript">
									(function($) {
										$(function() {
											$('#wf-install-license').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												WFAD.setOption(
													'apiKey',
													$('#wf-license-input').val(),
													function() {
														delete WFAD.pendingChanges['apiKey'];
														WFAD.updatePendingChanges();
														window.location.reload(true);
													},
													function() {
														window.location.reload();
													},
													true
												);
											});

											$('#wf-license-input').on('focus', function() {
												var field = $(this);
												setTimeout(function() {
													field.select();
												}, 100);
											}).on('change paste keyup', function() {
												setTimeout(function() {
													var originalKey = $('#wf-license-input').closest('.wf-option').data('originalTextValue');
													if (originalKey != $('#wf-license-input').val()) {
														$('#wf-license-controls a').hide();
														$('#wf-install-license').show();
													}
												}, 100);
											});

											$(window).on('wfOptionsReset', function() {
												$('#wf-license-controls a').show();
												$('#wf-install-license').hide();
											});

											$('.wf-downgrade-license').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												var prompt = $('#wfTmpl_downgradePrompt').tmpl();
												var promptHTML = $("<div />").append(prompt).html();
												WFAD.colorboxHTML('400px', promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
													$('#wf-downgrade-prompt-cancel').on('click', function(e) {
														e.preventDefault();
														e.stopPropagation();

														WFAD.colorboxClose();
													});

													$('#wf-downgrade-prompt-downgrade').on('click', function(e) {
														e.preventDefault();
														e.stopPropagation();

														WFAD.ajax('wordfence_downgradeLicense', {}, function(res) {
															window.location.reload(true);
														});
													});
												}});
											});
										});
									})(jQuery);
								</script>
							</li>
							<?php if (wfLicense::current()->getKeyType() === wfLicense::KEY_TYPE_PAID_DELETED): ?>
							<li>
								<p><?php echo wp_kses(__('This was a premium license key, but it is no longer valid, so premium features are disabled. You can either remove the invalid key and continue using Wordfence\'s free features, or enter a new premium key to upgrade. If you have questions, contact <a href="mailto:billing@wordfence.com">billing@wordfence.com</a>.', 'wordfence'), array('a' => array('href' => array()))) ?></p>
							</li>
							<?php endif ?>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end license options -->
<script type="text/x-jquery-template" id="wfTmpl_downgradePrompt">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Confirm Reset', 'wordfence'),
		'messageHTML' => wp_kses(__('<p>Are you sure you want to reset this site\'s Wordfence License? This will disable Premium features and return the site to the free version of Wordfence. Your settings will still be retained when reinstalling a license.</p><p>If autorenew is enabled for the current license, the license will renew at the next expiration date. If you would like to turn renewal off or assign the license to another site, log into wordfence.com to change it.</p>', 'wordfence'), array('p'=>array())),
		'primaryButton' => array('id' => 'wf-downgrade-prompt-cancel', 'label' => __('Cancel', 'wordfence'), 'link' => '#'),
		'secondaryButtons' => array(array('id' => 'wf-downgrade-prompt-downgrade', 'label' => __('Reset', 'wordfence'), 'link' => '#')),
	))->render();
	?>
</script>