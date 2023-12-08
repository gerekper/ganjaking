<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Search;
use ACP;
use Yoast;
use Yoast\WP\SEO\Config\Schema_Types;

class SchemaArticleType extends AC\Column\Meta implements ACP\Search\Searchable, ACP\Editing\Editable,
                                                          ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable,
                                                          ACP\Export\Exportable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    private $article_types;

    public function __construct()
    {
        $this->set_group('yoast-seo')
             ->set_label(__('Schema Article type', 'codepress-admin-columns'))
             ->set_type('column-yoast_article_type')
             ->set_article_types();
    }

    private function set_article_types(): void
    {
        $options = [];

        if (class_exists(Schema_Types::class)) {
            foreach ((new Yoast\WP\SEO\Config\Schema_Types())->get_article_type_options() as $option) {
                $options[$option['value']] = $option['name'];
            }
        }

        $this->article_types = $options;
    }

    private function get_article_type_label($value): string
    {
        return array_key_exists($value, $this->article_types)
            ? $this->article_types[$value]
            : $value;
    }

    public function get_value($id): string
    {
        $raw_value = $this->get_raw_value($id);

        return $raw_value
            ? $this->get_article_type_label($raw_value)
            : $this->get_empty_char();
    }

    public function get_meta_key(): string
    {
        return '_yoast_wpseo_schema_article_type';
    }

    public function is_valid(): bool
    {
        if ( ! class_exists('Yoast\WP\SEO\Helpers\Schema\Article_Helper')) {
            return false;
        }

        return (new Yoast\WP\SEO\Helpers\Schema\Article_Helper())->is_article_post_type($this->get_post_type());
    }

    public function search(): ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Meta\Select($this->get_meta_key(), $this->article_types);
    }

    public function sorting()
    {
        $article_types = $this->article_types;
        natcasesort($article_types);

        return (new ACP\Sorting\Model\MetaMappingFactory())->create(
            AC\MetaType::POST,
            $this->get_meta_key(),
            array_keys($article_types)
        );
    }

    public function editing(): ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Select($this->article_types),
            new ACP\Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function export(): ACP\Export\Service
    {
        return new ACP\Export\Model\Value($this);
    }

}