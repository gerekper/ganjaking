<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Advanced Scan Options group.
 *
 * Expects $scanner and $stateKey.
 *
 * @var wfScanner $scanner
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
						<strong><?php esc_html_e('Advanced Scan Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure" role="checkbox" aria-checked="<?php echo (wfPersistenceController::shared()->isActive($stateKey) ? 'true' : 'false'); ?>" tabindex="0"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<?php
						echo wfView::create('options/option-textarea', array(
							'textOptionName' => 'scan_exclude',
							'textValue' => wfUtils::cleanupOneEntryPerLine(wfConfig::get('scan_exclude')),
							'title' => __('Exclude files from scan that match these wildcard patterns (one per line)', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_EXCLUDE_PATTERNS),
							'noSpacer' => true,
						))->render();
						?>
					</li>
					<li>
						<?php
						echo wfView::create('options/option-textarea', array(
							'textOptionName' => 'scan_include_extra',
							'textValue' => wfConfig::get('scan_include_extra'),
							'title' => __('Additional scan signatures (one per line)', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_CUSTOM_MALWARE_SIGNATURES),
							'noSpacer' => true,
						))->render();
						?>
					</li>
					<li>
						<?php
							echo wfView::create('options/option-toggled', array(
								'optionName' => 'scan_force_ipv4_start',
								'enabledValue' => 1,
								'disabledValue' => 0,
								'value' => wfConfig::get('scan_force_ipv4_start') ? 1 : 0,
								'title' => __('Use only IPv4 to start scans', 'wordfence'),
								'subtitle' =>  __('This option requires cURL. (This may have no effect on some old PHP or cURL versions.)', 'wordfence'),
								'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_USE_ONLY_IPV4),
								'premium' => false,
								'disabled' => !wfUtils::isCurlSupported()
							))->render();
						?>
					</li>
					<li>
						<?php
						$options = array();
						foreach (range(0, wfScanMonitor::MAX_RESUME_ATTEMPTS) as $number) {
							$options[] = array('value' => $number, 'label' => $number > 0 ? $number : '0 (Disabled)');
						}
						echo wfView::create('options/option-select', array(
							'selectOptionName' => 'scan_max_resume_attempts',
							'selectOptions' => $options,
							'selectValue' => wfConfig::get('scan_max_resume_attempts', wfScanMonitor::DEFAULT_RESUME_ATTEMPTS),
							'title' => __('Maximum number of attempts to resume each scan stage', 'wordfence'),
							'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_SCAN_OPTION_MAX_RESUME_ATTEMPTS),
						))->render();
						?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end custom scan options -->