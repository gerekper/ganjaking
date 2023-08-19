<?php

namespace ACP\Admin\Page;

use AC;
use AC\Renderable;
use AC\View;

class Addons extends AC\Admin\Page\Addons
{

    protected function render_actions(AC\Integration $addon): ?Renderable
    {
        return $addon->is_plugin_active()
            ? (new View())->set_template('admin/page/component/addon/active-label')
            : null;
    }

    protected function get_grouped_addons(): array
    {
        $groups = [];

        $active = $this->integrations->find_all_active();

        if ($active->exists()) {
            $groups[] = [
                'title'        => __('Active', 'codepress-admin-columns'),
                'class'        => 'active',
                'integrations' => $active,
            ];
        }

        $not_active = $this->integrations->find_all_by_inactive_plugins();

        if ($not_active->exists()) {
            $groups[] = [
                'title'        => __('Available', 'codepress-admin-columns'),
                'class'        => 'available',
                'integrations' => $not_active,
            ];
        }

        return $groups;
    }

}