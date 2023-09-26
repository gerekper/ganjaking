<?php

declare(strict_types=1);

namespace ACP\Service;

use AC\Message;
use AC\Registerable;
use AC\Type\Url\Documentation;

class PluginNotice implements Registerable
{

    public function register(): void
    {
        $integrations = [
            'ac-addon-acf/ac-addon-acf.php',
            'ac-addon-buddypress/ac-addon-buddypress.php',
            'ac-addon-events-calendar/ac-addon-events-calendar.php',
            'ac-addon-gravityforms/ac-addon-gravityforms.php',
            'ac-addon-jetengine/ac-addon-jetengine.php',
            'ac-addon-media-library-assistant/ac-addon-media-library-assistant.php',
            'ac-addon-metabox/ac-addon-metabox.php',
            'ac-addon-pods/ac-addon-pods.php',
            'ac-addon-types/ac-addon-types.php',
            'ac-addon-woocommerce/ac-addon-woocommerce.php',
            'ac-addon-yoast-seo/ac-addon-yoast-seo.php',
        ];

        $message = sprintf(
            __(
                'This integration add-on is no longer required by %s and can be safely removed.',
                'codepress-admin-columns'
            ),
            'Admin Columns Pro'
        );
        $message .= sprintf(
            ' <a target="_blank" href="%s">%s</a>',
            Documentation::create_with_path(Documentation::ARTICLE_RELEASE_6),
            'Learn more &raquo;'
        );

        foreach ($integrations as $basename) {
            $notice = new Message\Plugin(
                $message,
                $basename,
                Message::INFO
            );
            $notice->register();
        }
    }

}