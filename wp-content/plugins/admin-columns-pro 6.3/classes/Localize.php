<?php

namespace ACP;

use AC\Asset\Location\Absolute;
use AC\Registerable;

class Localize implements Registerable
{

    private const TEXTDOMAIN = 'codepress-admin-columns';

    private $location;

    public function __construct(Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        add_action('init', [$this, 'localize']);
    }

    public function localize(): void
    {
        // prevent the loading of existing translations within the 'wp-content/languages' folder.
        unload_textdomain(self::TEXTDOMAIN);

        $local = $this->get_local();

        $this->load_textdomain($this->location->with_suffix('admin-columns/languages')->get_path(), $local);
        $this->load_textdomain($this->location->with_suffix('languages')->get_path(), $local);
    }

    private function get_local(): string
    {
        $local = function_exists('determine_locale')
            ? determine_locale()
            : get_user_locale();

        return (string)apply_filters('plugin_locale', $local, self::TEXTDOMAIN);
    }

    /**
     * Do no use `load_plugin_textdomain()` because it could prevent
     * pro languages from loading when core translation files are found.
     */
    private function load_textdomain(string $language_dir, string $local): void
    {
        $mofile = sprintf(
            '%s/%s-%s.mo',
            $language_dir,
            self::TEXTDOMAIN,
            $local
        );

        load_textdomain(self::TEXTDOMAIN, $mofile);
    }

}