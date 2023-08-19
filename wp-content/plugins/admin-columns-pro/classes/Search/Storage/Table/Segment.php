<?php

declare(strict_types=1);

namespace ACP\Search\Storage\Table;

use AC\Storage\Table;

final class Segment extends Table
{

    public const ID = 'id';
    public const KEY = 'key';
    public const LIST_SCREEN_ID = 'list_screen_id';
    public const USER_ID = 'user_id';
    public const NAME = 'name';
    public const URL_PARAMETERS = 'url_parameters';
    public const DATE_CREATED = 'date_created';

    public function get_name(): string
    {
        global $wpdb;

        return $wpdb->prefix . 'ac_segments';
    }

    public function get_schema(): string
    {
        global $wpdb;

        $collate = $wpdb->get_charset_collate();

        return "
			CREATE TABLE " . $this->get_name() . " (
				`" . self::ID . "` bigint(20) unsigned NOT NULL auto_increment,
				`" . self::KEY . "` char(13) NOT NULL,
				`" . self::LIST_SCREEN_ID . "` varchar(20) NOT NULL default '',
				`" . self::USER_ID . "` bigint(20),
				`" . self::NAME . "` varchar(255) NOT NULL default '',
				`" . self::URL_PARAMETERS . "` mediumtext,
				`" . self::DATE_CREATED . "` datetime NOT NULL,
				PRIMARY KEY (`" . self::ID . "`),
				UNIQUE (`" . self::KEY . "`)
			) $collate
		";
    }

}