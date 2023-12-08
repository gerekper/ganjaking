<?php

namespace ACP\Search\Comparison\User;

use AC;
use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Languages extends Comparison\Meta
    implements Comparison\RemoteValues
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, 'locale');
    }

    public function format_label(string $value): string
    {
        $translations = (new AC\Helper\User())->get_translations_remote();

        return $translations[$value]['native_name'] ?? $value;
    }

    private function get_language_options()
    {
        $options = [];

        foreach (get_available_languages() as $language) {
            $options[$language] = $this->format_label($language);
        }

        natcasesort($options);

        return $options;
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array($this->get_language_options());
    }

}