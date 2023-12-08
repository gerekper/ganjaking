<?php
declare(strict_types=1);

namespace ACP\ConditionalFormat\Settings\ListScreen;

use ACP;
use ACP\Settings;
use ACP\Settings\ListScreen\HideOnScreen;

final class HideOnScreenFactory implements Settings\ListScreen\HideOnScreenFactory
{

    public function create(): ACP\Settings\ListScreen\HideOnScreen
    {
        return new HideOnScreen(
            'hide_conditional_formatting',
            __('Conditional Formatting', 'codepress-admin-columns')
        );
    }

}