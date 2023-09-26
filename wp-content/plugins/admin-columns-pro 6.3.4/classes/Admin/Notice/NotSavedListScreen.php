<?php

namespace ACP\Admin\Notice;

use AC\ListScreen;
use AC\Message;
use AC\Registerable;

class NotSavedListScreen implements Registerable
{

    public function register(): void
    {
        add_action('ac/settings/notice', [$this, 'render_notice']);
    }

    public function render_notice(ListScreen $list_screen): void
    {
        if ( ! $list_screen->has_id()) {
            $message = sprintf(
                '%s %s',
                sprintf(
                    __('These settings are %s.', 'codepress-admin-columns'),
                    sprintf('<strong>%s</strong>', __('inactive', 'codepress-admin-columns'))
                ),
                sprintf(
                    __(
                        'Click %s to apply these column settings to the %s list table.',
                        'codepress-admin-columns'
                    ),
                    sprintf('<strong>%s</strong>', __('Save', 'codepress-admin-columns')),
                    sprintf(
                        '<strong>%s</strong>',
                        esc_html($list_screen->get_title() ?: $list_screen->get_label())
                    )
                )
            );
            $message = sprintf('<p>%s</p>', $message);

            $notice = new Message\InlineMessage($message, Message::INFO);

            echo $notice->render();
        }
    }

}