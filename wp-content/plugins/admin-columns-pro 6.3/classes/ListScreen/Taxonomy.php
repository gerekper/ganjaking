<?php

declare(strict_types=1);

namespace ACP\ListScreen;

use AC;
use AC\ColumnRepository;
use AC\MetaType;
use AC\Type\Uri;
use AC\WpListTableFactory;
use ACP\Column;
use ACP\Editing;
use ACP\Editing\BulkDelete\Deletable;
use ACP\Export;
use ACP\Filtering;
use ACP\Sorting;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Strategy;

class Taxonomy extends AC\ListScreen
    implements Editing\ListScreen, Export\ListScreen, Filtering\ListScreen, Sorting\ListScreen,
               Editing\BulkDelete\ListScreen, AC\ListScreen\ManageValue, AC\ListScreen\ListTable
{

    public const KEY_PREFIX = 'wp-taxonomy_';

    private $taxonomy;

    public function __construct(string $taxonomy)
    {
        parent::__construct(self::KEY_PREFIX . $taxonomy, 'edit-' . $taxonomy);

        $this->taxonomy = $taxonomy;
        $this->group = 'taxonomy';

        $this->set_meta_type(MetaType::TERM);
    }

    public function get_taxonomy(): string
    {
        return $this->taxonomy;
    }

    public function list_table(): AC\ListTable
    {
        return new AC\ListTable\Taxonomy(
            (new WpListTableFactory())->create_taxonomy_table($this->get_screen_id()),
            $this->taxonomy
        );
    }

    public function manage_value(): AC\Table\ManageValue
    {
        return new AC\Table\ManageValue\Taxonomy($this->taxonomy, new ColumnRepository($this));
    }

    public function get_label(): ?string
    {
        return $this->get_taxonomy_label_var('name');
    }

    public function get_singular_label(): ?string
    {
        return $this->get_taxonomy_label_var('singular_name');
    }

    private function get_post_type_tax(): ?string
    {
        $post_type = null;

        $object_type = $this->get_taxonomy_var('object_type');

        if ($object_type && post_type_exists(reset($object_type))) {
            $post_type = (string)$object_type[0];
        }

        return $post_type;
    }

    public function get_table_url(): Uri
    {
        return new AC\Type\Url\ListTable\Taxonomy(
            $this->taxonomy,
            $this->has_id() ? $this->get_id() : null,
            $this->get_post_type_tax()
        );
    }

    private function get_taxonomy_label_var($var)
    {
        $taxonomy = get_taxonomy($this->taxonomy);

        return $taxonomy->labels->{$var} ?? null;
    }

    private function get_taxonomy_var(string $var)
    {
        $taxonomy = get_taxonomy($this->taxonomy);

        return $taxonomy->{$var} ?? null;
    }

    protected function register_column_types(): void
    {
        $this->register_column_types_from_list([
            Column\CustomField::class,
            Column\Actions::class,
            Column\Taxonomy\Count::class,
            Column\Taxonomy\CountForPostType::class,
            Column\Taxonomy\CustomDescription::class,
            Column\Taxonomy\Description::class,
            Column\Taxonomy\Excerpt::class,
            Column\Taxonomy\ID::class,
            Column\Taxonomy\Links::class,
            Column\Taxonomy\Menu::class,
            Column\Taxonomy\Name::class,
            Column\Taxonomy\Posts::class,
            Column\Taxonomy\Slug::class,
            Column\Taxonomy\TaxonomyParent::class,
        ]);
    }

    public function editing()
    {
        return new Editing\Strategy\Taxonomy();
    }

    public function deletable()
    {
        return new Deletable\Taxonomy($this->taxonomy);
    }

    public function filtering($model)
    {
        return new Filtering\Strategy\Taxonomy($model);
    }

    public function sorting(AbstractModel $model): Strategy
    {
        return new Sorting\Strategy\Taxonomy($model, $this->taxonomy);
    }

    public function export()
    {
        return new Export\Strategy\Taxonomy($this);
    }

}