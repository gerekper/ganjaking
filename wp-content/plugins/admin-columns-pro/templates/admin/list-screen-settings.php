<?php

use AC\Form\Element\Select;
use AC\Form\Element\Toggle;
use ACP\ListScreenPreferences;
use ACP\Sorting\Settings\ListScreen\PreferredSort;

if ( ! defined('ABSPATH')) {
    exit;
}

?>
<section class="ac-setbox ac-ls-settings ac-section" data-section="ls-settings">
	<header class="ac-section__header">
		<div class="ac-setbox__header__title"><?= esc_html__('Settings', 'codepress-admin-columns') ?>
			<small>(<?= esc_html__('optional', 'codepress-admin-columns') ?>)</small>
		</div>
	</header>
	<form data-form-part="preferences">

		<div class="ac-setbox__row">
			<div class="ac-setbox__row__th">
				<label><?= esc_html__('Conditionals', 'codepress-admin-columns') ?></label>
				<small><?= esc_html__(
                        'Make this table view available only for specific users or roles.',
                        'codepress-admin-columns'
                    ) ?></small>
			</div>
			<div class="ac-setbox__row__fields">
				<div class="ac-setbox__row__fields__inner">
					<fieldset>
						<div class="row roles">
							<label for="layout-roles-<?= esc_attr($this->id) ?>" class="screen-reader-text">
                                <?= esc_html__('Roles', 'codepress-admin-columns') ?>
								<span>(<?= esc_html__('optional', 'codepress-admin-columns') ?>)</span>
							</label>

                            <?php
                            echo $this->select_roles; ?>

						</div>
						<div class="row users">
							<label for="layout-users-<?= esc_attr($this->id) ?>" class="screen-reader-text">
                                <?= esc_html__('Users') ?>
								<span>(<?= esc_html__('optional', 'codepress-admin-columns') ?>)</span>
							</label>

                            <?= $this->select_users ?>
						</div>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="ac-setbox__row" id="hide-on-screen">
			<div class="ac-setbox__row__th">
				<label><?= esc_html__('Table Elements', 'codepress-admin-columns') ?></label>
				<small><?= esc_html__(
                        'Show or hide elements from the table list screen.',
                        'codepress-admin-columns'
                    ) ?></small>
			</div>
			<div class="ac-setbox__row__fields">
				<div class="ac-setbox__row__fields__inner">
					<div class="checkbox-labels checkbox-labels vertical">

						<div data-component="acp-table-elements"></div>

					</div>
				</div>
			</div>
		</div>

        <?php
        $number_of_preferences = count(
            array_filter([$this->can_sort, $this->can_bookmark, $this->can_horizontal_scroll])
        );
        ?>

        <?php
        if ($number_of_preferences > 0) : ?>

			<div class="ac-setbox__row">
				<div class="ac-setbox__row__th">
					<label><?= esc_html__('Preferences', 'codepress-admin-columns') ?></label>
					<small><?= esc_html__(
                            'Set default settings that users will see when they visit the list table.',
                            'codepress-admin-columns'
                        ) ?></small>
				</div>

				<div class="ac-setbox__row__fields -has-subsettings -subsetting-total-<?= $number_of_preferences ?>">
                    <?php
                    if ($this->can_horizontal_scroll) : ?>
                        <?php
                        $pref_hs = $this->preferences['horizontal_scrolling'] ?? 'off';
                        ?>
						<div class="ac-setbox__row -sub -horizontal-scrolling">
							<div class="ac-setbox__row__th">
								<label><?= esc_html__('Horizontal Scrolling', 'codepress-admin-columns') ?></label>
                                <?= $this->tooltip_hs->get_label() ?>
                                <?= $this->tooltip_hs->get_instructions() ?>
							</div>
							<div class="ac-setbox__row__fields">

								<div class="ac-setbox__row__fields__inner">
                                    <?php
                                    $toggle = new Toggle('horizontal_scrolling', '', $pref_hs === 'on', 'on', 'off');
                                    echo $toggle->render();
                                    ?>
								</div>
							</div>
						</div>
                    <?php
                    endif; ?>
                    <?php
                    if ($this->can_sort) : ?>
                        <?php
                        $selected_order_by = $this->preferences[PreferredSort::FIELD_SORTING] ?? 0;
                        $selected_order = $this->preferences[PreferredSort::FIELD_SORTING_ORDER] ?? 'asc';
                        ?>
						<div class="ac-setbox__row -sub -sorting" data-setting="sorting-preference">
							<div class="ac-setbox__row__th">
								<label><?= esc_html__('Sorting', 'codepress-admin-columns') ?></label>
							</div>
							<div class="ac-setbox__row__fields">
								<div class="ac-setbox__row__fields__inner">
									<div class="radio-labels">
                                        <?php
                                        $select = new Select(PreferredSort::FIELD_SORTING, [
                                            0 => __('Default'),
                                        ]);
                                        echo $select->set_attribute('data-sorting', $selected_order_by);

                                        $select = new Select(PreferredSort::FIELD_SORTING_ORDER, [
                                            'asc'  => __('Ascending'),
                                            'desc' => __('Descending'),
                                        ]);
                                        echo $select->set_class('sorting_order')->set_value($selected_order);

                                        ?>
									</div>
								</div>
							</div>
						</div>
                    <?php
                    endif; ?>

                    <?php
                    if ($this->can_bookmark) : ?>

                        <?php
                        $segments = $this->segments;

                        $selected_filter_segment = $this->preferences[ListScreenPreferences::FILTER_SEGMENT] ?? '';
                        ?>

						<div class="ac-setbox__row -sub -predefinedfilters" data-setting="filter-segment-preference">
							<div class="ac-setbox__row__th">
								<label><?= esc_html__('Pre-applied Filters', 'codepress-admin-columns') ?></label>
                                <?= $this->tooltip_filters->get_label() ?>
                                <?= $this->tooltip_filters->get_instructions() ?>
							</div>
							<div class="ac-setbox__row__fields">
								<div class="ac-setbox__row__fields__inner">
                                    <?php
                                    if ( ! empty($segments)): ?>
                                        <?php
                                        $select = new Select(
                                            ListScreenPreferences::FILTER_SEGMENT,
                                            ['' => __('Default', 'codepress-admin-columns')] + $segments
                                        );
                                        echo $select->set_value($selected_filter_segment);
                                        ?>
                                    <?php
                                    else: ?>
										<p class="ac-setbox__descriptive">
                                            <?= esc_html__(
                                                "No public saved filters available.",
                                                'codepress-admin-columns'
                                            ) ?>
										</p>
                                    <?php
                                    endif; ?>
								</div>
							</div>
						</div>

                    <?php
                    endif; ?>

                    <?php
                    if ($this->can_primary_column) : ?>

						<div class="ac-setbox__row -sub -primary-column">
							<div class="ac-setbox__row__th">
								<label><?= esc_html__('Primary Column', 'codepress-admin-columns') ?></label>
                                <?= $this->tooltip_primary_column->get_label() ?>
                                <?= $this->tooltip_primary_column->get_instructions() ?>
							</div>
							<div class="ac-setbox__row__fields">
								<div class="ac-setbox__row__fields__inner">
                                    <?php
                                    if ($this->primary_columns) : ?>
										<div class="radio-labels">
                                            <?php
                                            $select = new Select('primary_column', $this->primary_columns);

                                            echo $select->set_class('primary_column')->set_value($this->primary_column);
                                            ?>
										</div>
                                    <?php
                                    else : ?>
										<p class="ac-setbox__descriptive">
                                            <?= esc_html__(
                                                "Remove actions column to change the primary column.",
                                                'codepress-admin-columns'
                                            ) ?>
										</p>
                                    <?php
                                    endif; ?>
								</div>
							</div>
						</div>

                    <?php
                    endif; ?>

					<div class="ac-setbox__row -sub -wrapping">
						<div class="ac-setbox__row__th">
							<label><?= esc_html__('Wrapping', 'codepress-admin-columns') ?></label>
                            <?= $this->wrapping_tooltip->get_label() ?>
                            <?= $this->wrapping_tooltip->get_instructions() ?>
						</div>
						<div class="ac-setbox__row__fields">
							<div class="ac-setbox__row__fields__inner">
								<div class="radio-labels">
                                    <?php
                                    $select = new Select('wrapping', [
                                        'wrap' => sprintf(
                                            '%s (%s)',
                                            __('Wrap', 'codepress-admin-columns'),
                                            __('default', 'codepress-admin-columns')
                                        ),
                                        'clip' => __('Clip', 'codepress-admin-columns'),
                                    ]);

                                    echo $select->set_class('wrapping')->set_value($this->wrapping);
                                    ?>
								</div>

							</div>
						</div>
					</div>

				</div>
			</div>

        <?php
        endif; ?>

	</form>
</section>