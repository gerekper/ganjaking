<?php

namespace ACA\ACF\Settings;

use AC;
use ACA\ACF;
use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\Field\Type;
use ACP;

class SettingFactory
{

    public function create(Field $field, Column $column): array
    {
        switch (true) {
            case $field instanceof Type\Number:
                return [new AC\Settings\Column\NumberFormat($column)];
            case $field instanceof Type\FlexibleContent:
                return [new ACF\Settings\Column\FlexibleContent($column)];
            case $field instanceof Type\Gallery:
                return [new AC\Settings\Column\Images($column)];
            case $field instanceof Type\Image:
                return [new AC\Settings\Column\Image($column)];
            case $field instanceof Type\Oembed:
                return [new ACF\Settings\Column\Oembed($column)];
            case $field instanceof Type\Password:
                return [new AC\Settings\Column\Password($column)];
            case $field instanceof Type\PageLinks:
                return [new ACF\Settings\Column\PageLink($column)];
            case $field instanceof Type\PostObject:
            case $field instanceof Type\Relationship:
                $settings = [new AC\Settings\Column\Post($column)];

                if ($field->is_multiple()) {
                    $settings[] = new AC\Settings\Column\NumberOfItems($column);
                    $settings[] = new AC\Settings\Column\Separator($column);
                }

                return $settings;
            case $field instanceof Type\Date:
            case $field instanceof Type\DateTime:
                return [
                    new ACF\Settings\Column\Date($column),
                    new ACP\Filtering\Settings\Date($column),
                ];
            case $field instanceof Type\Text:
                return [new AC\Settings\Column\CharacterLimit($column)];
            case $field instanceof Type\Textarea:
            case $field instanceof Type\Wysiwyg:
                return [new AC\Settings\Column\WordLimit($column)];
            case $field instanceof Type\Time:
                return [new ACF\Settings\Column\Time($column)];
            case $field instanceof Type\User:
                $settings = [new AC\Settings\Column\User($column)];

                if ($field->is_multiple()) {
                    $settings[] = new AC\Settings\Column\NumberOfItems($column);
                    $settings[] = new AC\Settings\Column\Separator($column);
                }

                return $settings;
            case $field instanceof Type\Url:
                return [new AC\Settings\Column\LinkLabel($column)];
            case $field instanceof Type\Select:
                $settings = [];

                if ($field->is_multiple()) {
                    $settings[] = new AC\Settings\Column\NumberOfItems($column);
                    $settings[] = new AC\Settings\Column\Separator($column);
                }

                return $settings;
            case $field instanceof Type\Taxonomy:
                $settings = [
                    new AC\Settings\Column\Term($column),
                    new AC\Settings\Column\TermLink($column),
                ];

                if ($field->is_multiple()) {
                    $settings[] = new AC\Settings\Column\NumberOfItems($column);
                    $settings[] = new AC\Settings\Column\Separator($column);
                }

                return $settings;
            default:
                return [];
        }
    }

}