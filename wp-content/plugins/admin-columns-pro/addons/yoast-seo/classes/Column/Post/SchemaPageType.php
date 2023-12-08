<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Search;
use ACP;
use Yoast;
use Yoast\WP\SEO\Config\Schema_Types;

class SchemaPageType extends AC\Column\Meta implements ACP\Search\Searchable, ACP\Editing\Editable,
                                                       ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable,
                                                       ACP\Export\Exportable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    private $page_types;

    public function __construct()
    {
        $this->set_group('yoast-seo')
             ->set_label(__('Schema Page type', 'codepress-admin-columns'))
             ->set_type('column-yoast_page_type')
             ->set_page_types();
    }

    private function set_page_types(): void
    {
        $options = [];

        if (class_exists(Schema_Types::class)) {
            foreach ((new Yoast\WP\SEO\Config\Schema_Types())->get_page_type_options() as $option) {
                $options[$option['value']] = $option['name'];
            }
        }

        natcasesort($options);

        $this->page_types = $options;
    }

    private function get_page_type_label($value): string
    {
        return array_key_exists($value, $this->page_types)
            ? $this->page_types[$value]
            : $value;
    }

    public function get_value($id): string
    {
        $raw_value = $this->get_raw_value($id);

        return $raw_value
            ? $this->get_page_type_label($raw_value)
            : $this->get_empty_char();
    }

    public function get_meta_key(): string
    {
        return '_yoast_wpseo_schema_page_type';
    }

    public function search(): ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Meta\Select($this->get_meta_key(), $this->page_types);
    }

    public function sorting()
    {
        return (new ACP\Sorting\Model\MetaMappingFactory())->create(
            AC\MetaType::POST,
            $this->get_meta_key(),
            array_keys($this->page_types)
        );
    }

    public function editing(): ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Select($this->page_types),
            new ACP\Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function export(): ACP\Export\Service
    {
        return new ACP\Export\Model\Value($this);
    }

}